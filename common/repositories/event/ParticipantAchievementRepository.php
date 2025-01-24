<?php


namespace common\repositories\event;


use frontend\models\work\event\ParticipantAchievementWork;
use Yii;

class ParticipantAchievementRepository
{
    public function get($id)
    {
        return ParticipantAchievementWork::find()->where(['id' => $id])->one();
    }

    public function getByForeignEvent($foreignEventId)
    {
        return ParticipantAchievementWork::find()
            ->joinWith(['actParticipantWork actParticipantWork'])
            ->where(['actParticipantWork.foreign_event_id' => $foreignEventId])->all();
    }

    public function prepareCreate($actParticipantId, $achievement, $type, $certNumber, $nomination, $date)
    {
        $model = ParticipantAchievementWork::fill($actParticipantId, $achievement, $type, $certNumber, $nomination, $date);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}