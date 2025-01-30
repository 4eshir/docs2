<?php

namespace common\repositories\act_participant;

use app\models\work\team\ActParticipantBranchWork;
use common\models\scaffold\ActParticipantBranch;
use Yii;

class ActParticipantBranchRepository
{
    public function prepareCreate($actParticipantId, $branch){
        $model = ActParticipantBranchWork::fill($actParticipantId, $branch);
        $model->save();
        return $model->id;
    }
}