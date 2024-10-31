<?php

namespace common\repositories\team;

use app\models\work\team\TeamNameWork;
use app\models\work\team\TeamWork;
use common\models\scaffold\TeamName;
use Yii;

class TeamRepository
{
    public function prepareTeamNameCreate($name, $foreignEventId){
        $model = TeamNameWork::fill($name, $foreignEventId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function prepareTeamCreate($actParticipant, $foreignEventId, $participantId, $teamNameId){
        $model = TeamWork::fill($actParticipant,$foreignEventId, $participantId,$teamNameId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}