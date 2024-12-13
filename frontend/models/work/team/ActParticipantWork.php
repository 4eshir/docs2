<?php

namespace app\models\work\team;

use common\events\EventTrait;
use common\models\scaffold\ActParticipant;
use common\models\scaffold\SquadParticipant;
use frontend\models\work\general\PeopleWork;
use Yii;
use yii\helpers\ArrayHelper;

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
    public function getTeachers()
    {
        $firstTeacher = PeopleWork::findOne($this->teacher_id);
        $secondTeacher = PeopleWork::findOne($this->teacher2_id);
        return $firstTeacher->firstname . ' ' . $firstTeacher->surname . ' ' . $firstTeacher->patronymic. "\n" .
             $secondTeacher->firstname . ' ' . $secondTeacher->surname . ' ' . $secondTeacher->patronymic;
    }
    public function getTeam()
    {
        $team = TeamNameWork::findOne($this->team_name_id);
        return $team->name;
    }
    public function getParticipants(){
        $participants = [];
        $squadParticipants = SquadParticipant::findAll(['act_participant' => $this->id]);
        foreach($squadParticipants as $squadParticipant){
            $person = PeopleWork::findOne($squadParticipant["participant_id"]);
            $participants[] = $person['surname'] . ' ' . $person['firstname'] . ' ' . $person['patronymic']. "\n";

        }
        return $participants;
    }
    public function getTypeParticipant(){
        if($this->type == 1){
            return "Командный";
        }
        else {
            return "Личный";
        }
    }
    public function getFocusName(){
        return Yii::$app->focus->get($this->focus);
    }
    public function getBranchName(){
        return Yii::$app->branches->get($this->branch);
    }
    public function getFormName(){
        return Yii::$app->eventWay->get($this->form);
    }
}