<?php

namespace common\repositories\general;

use common\components\traits\CommonRepositoryFunctions;
use common\models\work\general\PeoplePositionCompanyBranchWork;
use common\models\work\general\PositionWork;
use yii\helpers\ArrayHelper;

class PeoplePositionCompanyBranchRepository
{
    use CommonRepositoryFunctions;

    public function getByPeople($peopleId)
    {
        return PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
    }

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

    public function getPositionsByPeople($peopleId)
    {
        $peoplePositions = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
        return PositionWork::find()->where(['IN', 'id', ArrayHelper::getColumn($peoplePositions, 'position_id')])->all();
    }

    public function getCompaniesByPeople($peopleId)
    {
        $peoplePositions = PeoplePositionCompanyBranchWork::find()->where(['people_id' => $peopleId])->all();
        return PositionWork::find()->where(['IN', 'id', ArrayHelper::getColumn($peoplePositions, 'company_id')])->all();
    }
}