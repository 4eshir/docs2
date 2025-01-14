<?php


namespace frontend\models\work\educational\journal;

use InvalidArgumentException;

class VisitLesson
{
    public int $lessonId;
    public int $status;

    public function __construct(
        int $lessonId,
        int $status
    )
    {
        $this->lessonId = $lessonId;
        $this->status = $status;
    }

    public function getLessonId()
    {
        return $this->lessonId;
    }

    public static function fromString(string $json) : array
    {
        $lessonsArray = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Invalid JSON string.");
        }

        $visitLessons = [];
        foreach ($lessonsArray as $item) {
            $visitLessons[] = new VisitLesson($item['lesson_id'], $item['status']);
        }

        return $visitLessons;
    }

    /**
     * @param VisitLesson[] $lessons
     * @return int[]
     */
    public static function getLessonIds(array $lessons)
    {
        return array_map(fn($obj) => $obj->getLessonId(), $lessons);
    }

    public function __toString()
    {
        return "{\"lesson_id\":\"$this->lessonId\", \"status\":\"$this->status\"}";
    }
}