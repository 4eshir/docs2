<?php

namespace common\repositories\dictionaries;

use common\components\traits\CommonRepositoryFunctions;
use common\helpers\SortHelper;
use common\repositories\general\PeoplePositionCompanyBranchRepository;
use DomainException;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\db\ActiveQuery;

class PeopleRepository
{
    use CommonRepositoryFunctions;

    private PeoplePositionCompanyBranchRepository $peoplePositionCompanyBranchRepository;

    public function __construct(PeoplePositionCompanyBranchRepository $peoplePositionCompanyBranchRepository)
    {
        $this->peoplePositionCompanyBranchRepository = $peoplePositionCompanyBranchRepository;
    }

    public function get($id)
    {
        return PeopleWork::find()->where(['id' => $id])->one();
    }

    public function getPositionsCompanies($id)
    {
        return $this->peoplePositionCompanyBranchRepository->getByPeople($id);
    }

    public function getCompaniesPositionsByPeople($peopleId)
    {
        return [
            $this->peoplePositionCompanyBranchRepository->getCompaniesByPeople($peopleId),
            $this->peoplePositionCompanyBranchRepository->getPositionsByPeople($peopleId)
        ];
    }

    /**
     * Возвращает сортированный список людей
     * @param int $orderedType тип сортировки
     * @param int $orderDirection направление сортировки @see standard_defines
     * @param ActiveQuery $baseQuery базовый запрос, который необходимо упорядочить (при наличии)
     * @return array|\yii\db\ActiveQuery|\yii\db\ActiveRecord[]
     */
    public function getOrderedList(int $orderedType = SortHelper::ORDER_TYPE_ID, int $orderDirection = SORT_DESC, $baseQuery = null)
    {
        $query = $baseQuery ?: PeopleWork::find();
        if (SortHelper::orderedAvailable(Yii::createObject(PeopleWork::class), $orderedType, $orderDirection)) {
            switch ($orderedType) {
                case SortHelper::ORDER_TYPE_ID:
                    $query->orderBy(['id' => $orderDirection]);
                    break;
                case SortHelper::ORDER_TYPE_FIO:
                    $query->orderBy(['surname' => $orderDirection, 'firstname' => $orderDirection, 'patronymic' => $orderDirection]);
                    break;
                default:
                    throw new DomainException('Что-то пошло не так');
            }
        }
        else {
            throw new DomainException('Невозможно произвести сортировку по таблице ' . PeopleWork::tableName());
        }

        return $query->all();
    }

    public function getPeopleFromMainCompany()
    {
        $query = PeopleWork::find()
            ->where(['IN', 'id', $this->peoplePositionCompanyBranchRepository->getPeopleByCompany(Yii::$app->params['mainCompanyId'])]);

        return $this->getOrderedList(SortHelper::ORDER_TYPE_FIO, SORT_ASC, $query);
    }

    public function save(PeopleWork $people)
    {
        if (!$people->save()) {
            throw new DomainException('Ошибка сохранения человека. Проблемы: '.json_encode($people->getErrors()));
        }

        return $people->id;
    }
}