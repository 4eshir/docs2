<?php

namespace common\models\work;

use common\helpers\StringFormatter;
use common\models\scaffold\Errors;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\event\EventRepository;
use common\repositories\event\ForeignEventRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\event\EventWork;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\team\ActParticipantWork;
use Yii;

class ErrorsWork extends Errors
{
    public static function fill(
        string $error,
        string $tableName,
        int $rowId,
        int $branch = null,
        string $createDatetime = '',
        int $wasAmnesty = 0
    ): ErrorsWork
    {
        if (StringFormatter::isEmpty($createDatetime)) {
            $createDatetime = date('Y-m-d H:i:s');
        }

        $entity = new static();
        $entity->error = $error;
        $entity->table_name = $tableName;
        $entity->table_row_id = $rowId;
        $entity->branch = $branch;
        $entity->create_datetime = $createDatetime;
        $entity->was_amnesty= $wasAmnesty;

        return $entity;
    }

    public function setAmnesty()
    {
        $this->was_amnesty = 1;
    }

    public function removeAmnesty()
    {
        $this->was_amnesty = 0;
    }

    /**
     * Возвращает место возникновения ошибки в виде строки
     *
     * @return string
     */
    public function getEntityName() : string
    {
        if ($this->table_name == TrainingGroupWork::tableName()) {
            /** @var TrainingGroupWork $group */
            $group = (Yii::createObject(TrainingGroupRepository::class))->get($this->table_row_id);
            return $group->number;
        }

        if ($this->table_name == TrainingProgramWork::tableName()) {
            /** @var TrainingProgramWork $program */
            $program = (Yii::createObject(TrainingProgramRepository::class))->get($this->table_row_id);
            return $program->name;
        }

        if ($this->table_name == EventWork::tableName()) {
            /** @var EventWork $event */
            $event = (Yii::createObject(EventRepository::class))->get($this->table_row_id);
            return $event->name;
        }

        if ($this->table_name == ForeignEventWork::tableName()) {
            /** @var ForeignEventWork $event */
            $event = (Yii::createObject(ForeignEventRepository::class))->get($this->table_row_id);
            return $event->name;
        }

        if ($this->table_name == ActParticipantWork::tableName()) {
            /** @var ActParticipantWork $act */
            $act = (Yii::createObject(ActParticipantRepository::class))->get($this->table_row_id);
            return "Акт участия в мероприятии {$act->foreignEventWork->name}";
        }

        return '';
    }
}