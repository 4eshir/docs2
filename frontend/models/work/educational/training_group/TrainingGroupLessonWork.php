<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TrainingGroupLesson;
use common\repositories\dictionaries\AuditoriumRepository;
use frontend\models\work\dictionaries\AuditoriumWork;
use Yii;

/**
 * @property AuditoriumWork $auditoriumWork
 */

class TrainingGroupLessonWork extends TrainingGroupLesson
{
    public $autoDate;
    public $auditoriumName;

    public static function fill($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration)
    {
        $entity = new static();
        $entity->training_group_id = $groupId;
        $entity->lesson_date = $lessonDate;
        $entity->lesson_start_time = $lessonStartTime;
        $entity->branch = $branch;
        $entity->auditorium_id = $auditoriumId;
        $entity->lesson_end_time = $lessonEndTime;
        $entity->duration = $duration;

        if ($auditoriumId !== null) {
            /** @var AuditoriumWork $auditorium */
            $auditorium = (Yii::createObject(AuditoriumRepository::class))->get($auditoriumId);
            $entity->auditoriumName = $auditorium->name . ' (' . Yii::$app->branches->get($auditorium->branch) . ')';
        }

        return $entity;
    }

    /**
     * Проверяет, достаточно ли данных для сохранения в БД
     * @return bool
     */
    public function isEnoughData()
    {
        return $this->training_group_id !== "" &&
               $this->lesson_date !== "" &&
               $this->lesson_start_time !== "" &&
               $this->branch !== "" &&
               $this->auditorium_id !== "" &&
               $this->lesson_end_time !== "" &&
               $this->duration !== "";
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['autoDate'], 'safe'],
        ]);
    }

    public function __toString()
    {
        return "[GroupID: $this->training_group_id]
                [Date: $this->lesson_date]
                [Start: $this->lesson_start_time]
                [End: $this->lesson_end_time]
                [Branch: $this->branch]
                [AudID: $this->auditorium_id]
                [Duration: $this->duration]";
    }

    public function setAuditoriumName()
    {
        if ($this->auditorium_id !== null) {
            /** @var AuditoriumWork $auditorium */
            $auditorium = (Yii::createObject(AuditoriumRepository::class))->get($this->auditorium_id);
            $this->auditoriumName = $auditorium->name . ' (' . Yii::$app->branches->get($auditorium->branch) . ')';
        }
    }
    public function getAuditoriumWork()
    {
        return $this->hasOne(AuditoriumWork::class, ['id' => 'auditorium_id']);
    }

}