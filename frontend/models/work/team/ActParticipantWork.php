<?php

namespace app\models\work\team;

use common\models\scaffold\ActParticipant;

class ActParticipantWork extends ActParticipant
{
    public static function fill(
        $participantId,
        $teacherId,
        $teacher2Id,
        $foreignEventId,
        $branch,
        $focus,
        $allowRemoteId,
        $nomination
    ){
        $entity = new static();
        $entity->participant_id = $participantId;
        $entity->teacher_id = $teacherId;
        $entity->teacher2_id = $teacher2Id;
        $entity->foreign_event_id = $foreignEventId;
        $entity->branch = $branch;
        $entity->focus = $focus;
        $entity->allow_remote_id = $allowRemoteId;
        $entity->nomination = $nomination;
        return $entity;
    }
}