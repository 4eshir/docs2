<?php


namespace frontend\models\work\educational\journal;


use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\providers\group_participant\TrainingGroupParticipantProvider;
use Yii;

class ParticipantLessons
{
    private TrainingGroupParticipantRepository $repository;

    public $participant;
    public int $trainingGroupParticipantId;
    /** @var VisitLesson[] $lessonIds */
    public array $lessonIds;

    public function __construct(
        int $trainingGroupParticipantId,
        array $lessonIds,
        TrainingGroupParticipantRepository $repository = null
    )
    {
        $this->trainingGroupParticipantId = $trainingGroupParticipantId;
        $this->lessonIds = $lessonIds;
        if (!$repository) {
            $repository = Yii::createObject(
                TrainingGroupParticipantRepository::class,
                ['provider' => Yii::createObject(TrainingGroupParticipantProvider::class)]
            );
        }
        /** @var TrainingGroupParticipantRepository $repository */
        $this->repository = $repository;

        $participantWork = $this->repository->get($this->trainingGroupParticipantId);
        $this->participant = $participantWork ? $participantWork->participantWork : null;
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