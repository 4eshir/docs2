<?php

namespace common\repositories\act_participant;

use frontend\models\work\team\ActParticipantBranchWork;
use Yii;
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
    public function prepareDeleteByAct($actParticipantId){
        $command = Yii::$app->db->createCommand();
        $command->delete(ActParticipantBranchWork::tableName(), ['act_participant_id' => $actParticipantId]);
        return $command->getRawSql();
    }
}