<?php

namespace common\repositories\act_participant;

use frontend\models\work\team\ActParticipantBranchWork;
use yii\helpers\ArrayHelper;

class ActParticipantBranchRepository
{
    public function getEventIdsByBranches(array $branches)
    {
        $actParticipants = ActParticipantBranchWork::find()
            ->joinWith(['actParticipantWork actParticipantWork'])
            ->where(['IN', 'branch', $branches])
            ->all();

        return array_unique(
            ArrayHelper::getColumn(
                $actParticipants,
                'actParticipantWork.foreign_event_id'
            )
        );
    }

    public function prepareCreate($actParticipantId, $branch)
    {
        $model = ActParticipantBranchWork::fill($actParticipantId, $branch);
        $model->save();
        return $model->id;
    }
}