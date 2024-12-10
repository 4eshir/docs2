<?php

namespace common\repositories\educational;

use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class TrainingGroupParticipantRepository
{
    public function get($id)
    {
        return TrainingGroupParticipantWork::find()->where(['id' => $id])->one();
    }

    public function prepareCreate($groupId, $participantId, $sendMethod)
    {
        $model = TrainingGroupParticipantWork::fill($groupId, $participantId, $sendMethod);
        $model->success = false;
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(TrainingGroupParticipantWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }
}