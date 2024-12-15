<?php

namespace common\repositories\team;

use app\models\work\team\TeamNameWork;
use app\models\work\team\TeamWork;
use common\models\scaffold\Team;
use Yii;

class TeamRepository
{
    public function getById($id)
    {
        return TeamNameWork::findOne($id);
    }
    public function getByForeignEventId($id)
    {
        return TeamWork::find()->where(['foreign_event_id' => $id])->all();
    }
    public function getByNameAndForeignEventId($id, $name)
    {
        return TeamNameWork::find()->andWhere(['foreign_event_id' => $id])->andWhere(['name' => $name])->one();
    }
    public function getNamesByForeignEventId($id)
    {
        return TeamNameWork::find()->where(['foreign_event_id' => $id])->all();
    }
    public function prepareTeamNameCreate($model ,$name, $foreignEventId){
        $model->name = $name;
        $model->foreign_event_id = $foreignEventId;
        $model->save();
        return $model->id;
    }
    public function prepareTeamCreate($actParticipant, $foreignEventId, $participantId, $teamNameId){
        $model = TeamWork::fill($actParticipant,$foreignEventId, $participantId,$teamNameId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}