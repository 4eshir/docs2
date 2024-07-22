<?php

namespace common\repositories\general;

use common\models\work\general\PeoplePositionBranchWork;
use common\models\work\general\PositionWork;
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
        $query = PositionWork::find();
        if ($peopleId) {
            $subQuery = PeoplePositionBranchWork::find()->where(['people_id' => $peopleId])->all();
            $query->andWhere(['IN', 'id', ArrayHelper::getColumn($subQuery, 'position_id')]);
        }

        return $query->all();
    }
}