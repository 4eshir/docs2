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

    public function getParticipantsFromGroup($groupId)
    {
        return TrainingGroupParticipantWork::find()->where(['training_group_id' => $groupId])->all();
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

    public function prepareUpdate($id, $participantId, $sendMethod)
    {
        $command = Yii::$app->db->createCommand();
        $command->update('training_group_participant', ['participant_id' => $participantId, 'send_method' => $sendMethod], "id = $id");
        return $command->getRawSql();
    }
}