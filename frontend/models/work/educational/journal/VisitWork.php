<?php

namespace frontend\models\work\educational\journal;

use common\models\scaffold\Visit;

class VisitWork extends Visit
{
    const NONE = 0;
    const ATTENDANCE = 1;
    const NO_ATTENDANCE = 2;
    const DISTANCE = 3;

    /** @var VisitLesson[] $visitLessons */
    public array $visitLessons;

    public static function fill(int $groupParticipantId, string $lessons = '')
    {
        $entity = new static();
        $entity->training_group_participant_id = $groupParticipantId;
        $entity->lessons = $lessons;

        return $entity;
    }

    public function fillLessons()
    {
        $lessonsArray = array_map(function ($lesson) {
            return [
                'lesson_id' => $lesson->lessonId,
                'status' => $lesson->status,
            ];
        }, $this->visitLessons);

        $this->lessons = json_encode($lessonsArray);
    }
}
