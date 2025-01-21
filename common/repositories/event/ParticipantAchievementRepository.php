<?php


namespace common\repositories\event;


use frontend\models\work\event\ParticipantAchievementWork;

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
}