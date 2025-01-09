<?php


namespace frontend\models\work\educational\journal;


class ParticipantLessons
{
    public int $participantId;
    /** @var VisitLesson[] $lessonIds */
    public array $lessonIds;

    public function __construct(
        int $participantId,
        array $lessonIds
    )
    {
        $this->participantId = $participantId;
        $this->lessonIds = $lessonIds;
    }
}