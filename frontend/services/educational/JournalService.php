<?php

namespace frontend\services\educational;

use common\components\compare\LessonGroupCompare;
use common\components\compare\ParticipantGroupCompare;
use common\components\traits\Math;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

class JournalService
{
    use Math;

    const JOURNAL_EMPTY = 0;
    const JOURNAL_EXIST = 1;

    private VisitRepository $visitRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    private TrainingGroupParticipantRepository $participantRepository;

    public function __construct(
        VisitRepository $visitRepository,
        TrainingGroupLessonRepository $lessonRepository,
        TrainingGroupParticipantRepository $participantRepository
    )
    {
        $this->visitRepository = $visitRepository;
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
        $visits = $this->visitRepository->getByTrainingGroup($groupId);
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
        $visits = $this->visitRepository->getByTrainingGroup($groupId);
        foreach ($visits as $visit) {
            $this->visitRepository->delete($visit);
        }

        // Создаем новый журнал
        foreach ($participants as $participant) {
            $visit = VisitWork::fill(
                $groupId,
                $participant->participant_id,
                TrainingGroupLessonWork::convertLessonsToJson($newLessons) ? : ''
            );
            $this->visitRepository->save($visit);
        }

        return true;
    }

    public function updateJournal($groupId, array $lessons, array $participants)
    {
        $currentLessons = $this->visitRepository->getLessonsFromGroup($groupId);
        $currentParticipants = $this->visitRepository->getParticipantsFromGroup($groupId);

        $addLessons = $this->setDifference($lessons, $currentLessons, LessonGroupCompare::class);
        $addParticipants = $this->setDifference($participants, $currentParticipants, ParticipantGroupCompare::class);

        $delLessons = $this->setDifference($currentLessons, $lessons, LessonGroupCompare::class);
        $delParticipants = $this->setDifference($currentParticipants, $participants, ParticipantGroupCompare::class);

        $lessonString = $this->createLessonString($groupId, $addLessons, $delLessons);
        var_dump($lessonString);
    }

    /**
     * @param $groupId
     * @param TrainingGroupLessonWork[] $addLessons
     * @param TrainingGroupLessonWork[] $delLessons
     * @return false|string
     */
    private function createLessonString($groupId, array $addLessons, array $delLessons)
    {
        $curLessonsString = $this->visitRepository->getByTrainingGroup($groupId)[0]->lessons;
        $curLessonsJson = json_decode($curLessonsString);

        $delLessonIds = array_map(function ($lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            return $lesson->id;
        }, $delLessons);

        if (!empty($delLessonIds)) {
            $curLessonsJson = array_filter($curLessonsJson, function ($lesson) use ($delLessonIds) {
                return !in_array($lesson->lessonId, $delLessonIds);
            });
        }

        foreach ($addLessons as $lesson) {
            $visitLesson = new VisitLesson(
                $lesson->id,
                VisitWork::NONE
            );
            $curLessonsJson[] = json_decode((string)$visitLesson, true);
        }

        return json_encode(array_values($curLessonsJson));
    }
}