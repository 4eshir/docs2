<?php


namespace frontend\models\work\educational\journal;


class ParticipantLessons
{
    public int $trainingGroupParticipantId;
    /** @var VisitLesson[] $lessonIds */
    public array $lessonIds;

    public function __construct(
        int $trainingGroupParticipantId,
        array $lessonIds
    )
    {
        $this->trainingGroupParticipantId = $trainingGroupParticipantId;
        $this->lessonIds = $lessonIds;
    }
}