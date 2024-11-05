<?php

namespace common\repositories\act_participant;

use app\models\work\team\ActParticipantWork;
use common\models\scaffold\ActParticipant;
use Yii;

class ActParticipantRepository
{
    public function getByForeignEventId($foreignEventId){
        return ActParticipant::find(['foreign_event_id' => $foreignEventId])->all();
    }
    public function prepareCreate($participantId, $teacherId, $teacher2Id, $foreignEventId, $branch, $focus, $allowRemoteId, $nomination)
    {
        $model = ActParticipantWork::fill($participantId, $teacherId, $teacher2Id, $foreignEventId,$branch,$focus, $allowRemoteId, $nomination);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}