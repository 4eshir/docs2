<?php

namespace frontend\services\educational;

use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

class JournalService
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
                return $this->createJournal($groupId, $lessons, $participants);
            case self::JOURNAL_EXIST:
                return $this->updateJournal($groupId, $lessons, $participants);
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

    /**
     * @param $groupId
     * @param TrainingGroupLessonWork[] $lessons
     * @param TrainingGroupParticipantWork[] $participants
     */
    public function createJournal($groupId, array $lessons, array $participants)
    {
        // Конвертируем занятия
        $newLessons = [];
        foreach ($lessons as $lesson) {
            $newLessons[] = new VisitLesson($lesson->id, VisitWork::NONE);
        }

        // Удаляем существующий журнал
        $visits = $this->repository->getByTrainingGroup($groupId);
        foreach ($visits as $visit) {
            $this->repository->delete($visit);
        }

        // Создаем новый журнал
        foreach ($participants as $participant) {
            $visit = VisitWork::fill(
                $groupId,
                $participant->participant_id,
                TrainingGroupLessonWork::convertLessonsToJson($newLessons) ? : ''
            );
            $this->repository->save($visit);
        }

        return true;
    }
}