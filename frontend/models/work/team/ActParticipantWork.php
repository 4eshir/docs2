<?php

namespace app\models\work\team;

use common\models\scaffold\ActParticipant;
use frontend\models\work\general\PeopleWork;

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
    public function getParticipant()
    {
        $person = PeopleWork::findOne($this->participant_id);
        return $person->firstname . ' ' . $person->surname . ' ' . $person->patronymic;
    }
    public function getTeam() {
        $team = TeamWork::find()->where([
            'act_participant' => $this->id,
        ])->one();
        /* @var TeamNameWork $teamName */
        $teamName = TeamNameWork::find()->where([
            'id' => $team->team_name_id,
        ])->one();
        return $teamName->name;
    }
}