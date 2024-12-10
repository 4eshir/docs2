<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TrainingGroupLesson;

class TrainingGroupLessonWork extends TrainingGroupLesson
{
    public $autoDate;

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

        return $entity;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['autoDate'], 'safe'],
        ]);
    }
}