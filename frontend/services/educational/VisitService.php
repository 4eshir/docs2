<?php


namespace frontend\services\educational;


use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;

class VisitService
{
    const JOURNAL_EMPTY = 0;
    const JOURNAL_EXIST = 1;

    private VisitRepository $repository;
    private TrainingGroupLessonRepository $lessonRepository;
    private TrainingGroupParticipantRepository $participantRepository;

    public function __construct(
        VisitRepository $repository,
        TrainingGroupLessonRepository $lessonRepository,
        TrainingGroupParticipantRepository $participantRepository
    )
    {
        $this->repository = $repository;
        $this->lessonRepository = $lessonRepository;
        $this->participantRepository = $participantRepository;
    }

    public function generateJournal($groupId)
    {
        $status = $this->checkJournalStatus($groupId);
        $lessons = $this->lessonRepository->getLessonsFromGroup($groupId);
        $participants = $this->participantRepository->getParticipantsFromGroup($groupId);

        switch ($status) {
            case self::JOURNAL_EMPTY:
                $this->repository->createJournal($groupId, $lessons, $participants);
                break;
            case self::JOURNAL_EXIST:
                $this->repository->updateJournal($groupId, $lessons, $participants);
                break;
            default:
                break;
        }
    }

    public function checkJournalStatus($groupId)
    {
        $visits = $this->repository->getByTrainingGroup($groupId);
        if (count($visits) > 0) {
            return self::JOURNAL_EXIST;
        }
        else {
            return self::JOURNAL_EMPTY;
        }
    }
}