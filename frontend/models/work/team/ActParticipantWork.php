<?php

namespace app\models\work\team;

use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\models\scaffold\ActParticipant;
use common\models\scaffold\SquadParticipant;
use frontend\models\work\general\PeopleWork;
use InvalidArgumentException;
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
    public function fillUpdate(
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
    )
    {
        $this->teacher_id = $teacherId;
        $this->teacher2_id = $teacher2Id;
        $this->team_name_id = $teamNameId;
        $this->branch = $branch;
        $this->focus = $focus;
        $this->type = $type;
        $this->nomination = $nomination;
        $this->foreign_event_id = $foreignEventId;
        $this->allow_remote = $allowRemote;
        $this->form = $form;
        return $this;
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
        if ($this->team_name_id && $this->type == 1) {
            $team = TeamNameWork::findOne($this->team_name_id);
            return $team->name;
        }
        else {
            return "Участие в командах не предусмотрено";
        }
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
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }
        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath($this::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath($this::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath($this::tableName(), FilesHelper::TYPE_APP);
                break;
        }
        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }
}