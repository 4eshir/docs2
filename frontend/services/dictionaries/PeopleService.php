<?php

namespace frontend\services\dictionaries;

use common\models\scaffold\DocumentIn;
use common\models\scaffold\DocumentOut;
use common\models\scaffold\People;
use common\models\scaffold\Regulation;
use common\models\User;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\general\UserRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\DatabaseService;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;

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

        return array_merge($docsIn, $docsOut, $regulation);
    }
}