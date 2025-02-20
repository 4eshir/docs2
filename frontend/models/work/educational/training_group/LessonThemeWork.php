<?php


namespace frontend\models\work\educational\training_group;


use common\models\scaffold\LessonTheme;

class LessonThemeWork extends LessonTheme
{
    public static function fill(
        int $trainingGroupLessonId,
        int $thematicPlanId,
        int $teacherId = null
    )
    {
        $entity = new static();
        $entity->training_group_lesson_id = $trainingGroupLessonId;
        $entity->thematic_plan_id = $thematicPlanId;
        $entity->teacher_id = $teacherId;

        return $entity;
    }
}