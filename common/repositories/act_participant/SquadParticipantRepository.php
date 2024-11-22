<?php

namespace common\repositories\act_participant;

use app\models\work\team\SquadParticipantWork;
use common\models\scaffold\SquadParticipant;
use Yii;

class SquadParticipantRepository
{
    public function prepareCreate($actParticipantId, $participantId){
        $model = SquadParticipantWork::fill($actParticipantId, $participantId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}