<?php

namespace app\models\work\team;

use common\events\EventTrait;
use common\models\scaffold\Team;

class TeamWork extends Team
{
    public static function fill(
        $actParticipant,
        $foreignEventId,
        $participantId,
        $teamNameId
    ){
        $entity = new static();
        $entity->act_participant = $actParticipant;
        $entity->foreign_event_id = $foreignEventId;
        $entity->participant_id = $participantId;
        $entity->team_name_id = $teamNameId;
        return $entity;
    }
    public function getName()
    {
        /* @var TeamNameWork $team */
        $team = TeamNameWork::find()->where(['id' => $this->team_name_id])->one();
        return $team->name;
    }
}