<?php

namespace common\models\work\general;

use yii\helpers\ArrayHelper;

class PeoplePositionCompanyBranchRepository
{
    public function getPeopleByCompany($companyId)
    {
        return ArrayHelper::getColumn(
            PeoplePositionCompanyBranchWork::find()->where(['company_id' => $companyId])->all(),
            'people_id'
        );
    }

    public function getPeopleByPosition($positionId)
    {
        return ArrayHelper::getColumn(
            PeoplePositionCompanyBranchWork::find()->where(['position_id' => $positionId])->all(),
            'people_id'
        );
    }
}