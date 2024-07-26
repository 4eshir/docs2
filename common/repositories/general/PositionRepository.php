<?php

namespace common\repositories\general;

use common\models\work\general\PeoplePositionCompanyBranchWork;
use common\models\work\general\PositionWork;
use yii\helpers\ArrayHelper;

class PositionRepository
{
    /**
     * Возвращает список должностей
     * @param int|null $peopleId если передан параметр, то возвращает список должностей человека @see PeoplePositionBranchWork
     * @return array
     */
    public function getList(int $peopleId = null): array
    {
        $query = PositionWork::find();
        if ($peopleId) {
            $subQuery = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
            $query->andWhere(['IN', 'id', ArrayHelper::getColumn($subQuery, 'position_id')]);
        }

        return $query->all();
    }
}