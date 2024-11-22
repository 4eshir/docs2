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
    public function prepareCreate($teacherId, $teacher2Id, $teamNameId, $foreignEventId, $branch, $focus, $type, $allowRemote, $nomination, $form)
    {
        $model = ActParticipantWork::fill($teacherId, $teacher2Id, $teamNameId, $foreignEventId, $branch, $focus, $type, $allowRemote, $nomination, $form);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function getOneByUniqueAttributes($teamNameId, $nomination, $foreignEventId)
    {
        return ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['nomination' => $nomination])
            ->one();
    }
    public function getAllByUniqueAttributes($teamNameId, $nomination, $foreignEventId)
    {
        return ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['nomination' => $nomination])
            ->all();
    }
    public function getByTypeAndForeignEventId($foreignEventId, $type)
    {
        return ActParticipantWork::find()->andWhere(['foreign_event_id' => $foreignEventId])->andWhere(['type' => $type])->all();
    }
}