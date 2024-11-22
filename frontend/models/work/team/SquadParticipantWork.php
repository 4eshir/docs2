<?php

namespace app\models\work\team;

use common\models\scaffold\SquadParticipant;

class SquadParticipantWork extends SquadParticipant
{
    public static function fill(
        $actParticipant,
        $participantId
    ){
        $entity = new static();
        $entity->act_participant_id = $actParticipant;
        $entity->participant_id = $participantId;
        return $entity;
    }
}