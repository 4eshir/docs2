<?php

namespace common\repositories\providers\group_participant;

use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class TrainingGroupParticipantProvider implements TrainingGroupParticipantProviderInterface
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

    public function delete(TrainingGroupParticipantWork $model)
    {
        return $model->delete();
    }

    public function save(TrainingGroupParticipantWork $participant)
    {
        if (!$participant->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и ученика. Проблемы: '.json_encode($participant->getErrors()));
        }
        return $participant->id;
    }
}