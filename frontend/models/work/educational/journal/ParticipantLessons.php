<?php


namespace frontend\models\work\educational\journal;


use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use Yii;

class ParticipantLessons
{
    public $participant;
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
        $this->participant =
            (Yii::createObject(TrainingGroupParticipantRepository::class))
            ->get($this->trainingGroupParticipantId)
            ->participantWork;
    }

    public function sortLessons()
    {
        usort($this->lessonIds, function(VisitLesson $a, VisitLesson $b) {
            $dateComparison = strtotime($a->lesson->lesson_date) <=> strtotime($b->lesson->lesson_date);
            if ($dateComparison === 0) {
                return strtotime($a->lesson->lesson_start_time) <=> strtotime($b->lesson->lesson_start_time);
            }
            return $dateComparison;
        });
    }
}