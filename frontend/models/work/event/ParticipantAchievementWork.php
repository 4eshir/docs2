<?php

namespace frontend\models\work\event;

use app\models\work\team\ActParticipantWork;
use common\models\scaffold\ParticipantAchievement;

/** @property ActParticipantWork $actParticipantWork */
class ParticipantAchievementWork extends ParticipantAchievement
{
    public static function fill(
        $actParticipantId,
        $achievement,
        $type,
        $certNumber,
        $nomination,
        $date
    )
    {
        $entity = new static();
        $entity->act_participant_id = $actParticipantId;
        $entity->achievement = $achievement;
        $entity->type = $type;
        $entity->cert_number = $certNumber;
        $entity->nomination = $nomination;
        $entity->date = $date;

        return $entity;
    }

    public function getActParticipantWork()
    {
        return $this->hasOne(ActParticipantWork::class, ['id' => 'act_participant_id']);
    }

}