<?php

namespace common\repositories\general;

use common\models\work\general\CompanyWork;
use common\models\work\general\PeoplePositionCompanyBranchWork;
use DomainException;
use yii\helpers\ArrayHelper;

class CompanyRepository
{
    /**
     * Возвращает список организаций
     * @param int|null $peopleId если передан параметр, то возвращает текущую организацию человека @see PeopleWork
     * @return array
     */
    public function getList(int $peopleId = null): array
    {
        $query = CompanyWork::find();
        if ($peopleId) {
            $subQuery = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
            $query->andWhere(['IN', 'id', ArrayHelper::getColumn($subQuery, 'company_id')]);
        }

        return $query->all();
    }

    public function fastCreateWithId(
        $id,
        $name,
        $shortName,
        $isContractor
    )
    {
        return CompanyWork::fastFillWithId($id, $name, $shortName, $isContractor);
    }

    public function save(CompanyWork $company)
    {
        if (!$company->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($company->getErrors()));
        }

        return $company->id;
    }
}