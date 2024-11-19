<?php

namespace common\repositories\act_participant;

use app\models\work\team\ActParticipantWork;
use common\models\scaffold\ActParticipant;
use Yii;

class ActParticipantRepository
{
    public function getByForeignEventId($foreignEventId){
        return ActParticipantWork::find()->where(['foreign_event_id' => $foreignEventId])->all();
    }
    public function prepareCreate($teacherId, $teacher2Id, $teamNameId, $foreignEventId, $branch, $focus, $type, $allowRemote, $nomination)
    {
        $model = ActParticipantWork::fill($teacherId, $teacher2Id, $teamNameId, $foreignEventId, $branch, $focus, $type, $allowRemote, $nomination);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}