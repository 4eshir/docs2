<?php

namespace frontend\models\work\event;

use app\models\work\team\ActParticipantWork;
use common\models\scaffold\ParticipantAchievement;

/** @property ActParticipantWork $actParticipantWork */
class ParticipantAchievementWork extends ParticipantAchievement
{
    public function getActParticipantWork()
    {
        return $this->hasOne(ActParticipantWork::class, ['id' => 'act_participant_id']);
    }

}