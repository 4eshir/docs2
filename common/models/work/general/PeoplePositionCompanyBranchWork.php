<?php

namespace common\models\work\general;

use common\models\scaffold\PeoplePositionCompanyBranch;

class PeoplePositionCompanyBranchWork extends PeoplePositionCompanyBranch
{
    public function getCompanyWork()
    {
        return $this->hasOne(CompanyWork::class, ['id' => 'company_id']);
    }

    public function getPositionWork()
    {
        return $this->hasOne(PositionWork::class, ['id' => 'position_id']);
    }

    public function getCompanyPositionString()
    {
        return $this->companyWork->name . " (" . $this->positionWork->name . ")";
    }
}
