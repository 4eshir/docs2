<?php


namespace common\repositories\event;


use DomainException;
use frontend\forms\event\ParticipantAchievementForm;
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

    public function save(ParticipantAchievementWork $achievement)
    {
        if (!$achievement->save()) {
            throw new DomainException('Ошибка сохранения положения. Проблемы: '.json_encode($achievement->getErrors()));
        }

        return $achievement->id;
    }

    public function delete(ParticipantAchievementWork $achievement)
    {
        return $achievement->delete();
    }
}