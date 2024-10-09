<?php

namespace frontend\services\dictionaries;

use app\events\dictionaries\PeoplePositionCompanyBranchEventCreate;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\DocumentIn;
use common\models\scaffold\DocumentOut;
use common\models\scaffold\People;
use common\models\scaffold\Regulation;
use common\models\User;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\general\UserRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\DatabaseService;
use DomainException;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\general\PeopleWork;
use PHPUnit\Util\Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class PeopleService implements DatabaseService
{
    private DocumentInRepository $documentInRepository;
    private DocumentOutRepository $documentOutRepository;
    private RegulationRepository $regulationRepository;
    private UserRepository $userRepository;

    public function __construct(
        DocumentInRepository $documentInRepository,
        DocumentOutRepository $documentOutRepository,
        RegulationRepository $regulationRepository,
        UserRepository $userRepository
    )
    {
        $this->documentInRepository = $documentInRepository;
        $this->documentOutRepository = $documentOutRepository;
        $this->regulationRepository = $regulationRepository;
        $this->userRepository = $userRepository;
    }

    public function createPositionsCompaniesArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var PeoplePositionCompanyBranchWork $item */
            $result[] = $item->companyWork->name . " (" . $item->positionWork->name . ")";
        }

        return $result;
    }

    public function attachPositionCompanyBranch(PeopleWork $model, array $positions, array $companies, array $branches)
    {
        if (!(count($positions) == count($companies) && count($companies) == count($branches))) {
            throw new DomainException('Размеры массивов $positions, $companies и $branches не совпадают');
        }

        for ($i = 0; $i < count($positions); $i++) {
            if ($positions[$i] !== "" && $companies[$i] !== "") {
                $model->recordEvent(new PeoplePositionCompanyBranchEventCreate($model->id, (int)$positions[$i],
                    (int)$companies[$i], (int)$branches[$i]),
                    PeoplePositionCompanyBranchWork::class);
            }
        }
    }

    /**
     * Возвращает список ошибок, если список пуст - проблем нет
     * @param $entityId
     * @return array
     */
    public function isAvailableDelete($entityId)
    {
        $docsIn = $this->documentInRepository->checkDeleteAvailable(DocumentIn::tableName(), People::tableName(), $entityId);
        $docsOut = $this->documentOutRepository->checkDeleteAvailable(DocumentOut::tableName(), People::tableName(), $entityId);
        $regulation = $this->regulationRepository->checkDeleteAvailable(Regulation::tableName(), People::tableName(), $entityId);
        $user = $this->userRepository->checkDeleteAvailable(User::tableName(), People::tableName(), $entityId);

        return array_merge($docsIn, $docsOut, $regulation, $user);
    }

    public function getPositionCompanyBranchTable(PeopleRepository $repository, int $modelId)
    {
        $branchNames = Yii::$app->branches->getList();
        $positionCompanyBranches = $repository->getPositionsCompanies($modelId);
        return HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Организация'], ArrayHelper::getColumn($positionCompanyBranches, 'companyWork.name')),
                array_merge(['Должность'], ArrayHelper::getColumn($positionCompanyBranches, 'positionWork.name')),
                array_merge(['Отдел (при наличии)'], array_map(function ($number) use ($branchNames) {
                    return $branchNames[$number] ?? null;
                }, ArrayHelper::getColumn($positionCompanyBranches, 'branch'))),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-position'),
                    ['id' => ArrayHelper::getColumn($positionCompanyBranches, 'id'), 'modelId' => array_fill(0, count($positionCompanyBranches), $modelId)])
            ]
        );
    }
}