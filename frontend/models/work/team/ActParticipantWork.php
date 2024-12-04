<?php

namespace app\models\work\team;

use common\events\EventTrait;
use common\models\scaffold\ActParticipant;
use frontend\models\work\general\PeopleWork;

class ActParticipantWork extends ActParticipant
{
    use EventTrait;
    public $actFiles;
    public static function fill(
        $teacherId,
        $teacher2Id,
        $teamNameId,
        $foreignEventId,
        $branch,
        $focus,
        $type,
        $allowRemote,
        $nomination,
        $form
    ){
        $entity = new static();
        $entity->teacher_id = $teacherId;
        $entity->teacher2_id = $teacher2Id;
        $entity->team_name_id = $teamNameId;
        $entity->branch = $branch;
        $entity->focus = $focus;
        $entity->type = $type;
        $entity->nomination = $nomination;
        $entity->foreign_event_id = $foreignEventId;
        $entity->allow_remote = $allowRemote;
        $entity->form = $form;
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