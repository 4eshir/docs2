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
use yii\helpers\ArrayHelper;

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
     */
    public function createJournal($groupId)
    {
        $lessons = $this->lessonRepository->getLessonsFromGroup($groupId);
        $participants = $this->participantRepository->getParticipantsFromGroup($groupId);

        // Конвертируем занятия
        $newLessons = [];
        foreach ($lessons as $lesson) {
            $newLessons[] = new VisitLesson($lesson->id, VisitWork::NONE);
        }

        $this->deleteJournal($groupId);

        // Создаем новый журнал
        foreach ($participants as $participant) {
            $visit = VisitWork::fill(
                $participant->id,
                TrainingGroupLessonWork::convertLessonsToJson($newLessons) ? : ''
            );
            $this->visitRepository->save($visit);
        }

        return true;
    }

    public function deleteJournal($groupId)
    {
        // Удаляем существующий журнал
        $visits = $this->visitRepository->getByTrainingGroup($groupId);
        foreach ($visits as $visit) {
            $this->visitRepository->delete($visit);
        }

        return true;
    }

    /**
     * @param $groupId
     * @param TrainingGroupLessonWork[] $addLessons
     * @param TrainingGroupLessonWork[] $delLessons
     * @return false|string
     */
    public function createLessonString($groupId, array $addLessons, array $delLessons)
    {
        $curLessonsString = $this->visitRepository->getByTrainingGroup($groupId)[0]->lessons;
        $curLessonsJson = json_decode($curLessonsString);

        $delLessonIds = array_map(function ($lesson) {
            /** @var TrainingGroupLessonWork $lesson */
            return $lesson->id;
        }, $delLessons);

        if (!empty($delLessonIds)) {
            $curLessonsJson = array_filter($curLessonsJson, function ($lesson) use ($delLessonIds) {
                return !in_array($lesson->lesson_id, $delLessonIds);
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

    public function setVisitStatus($trainingGroupParticipantId, $lessonId, $status)
    {
        $visit = $this->visitRepository->getByTrainingGroupParticipant($trainingGroupParticipantId);
        if ($visit) {
            $allLessons = VisitLesson::fromString($visit->lessons);
            $newLessons = [];
            foreach ($allLessons as $lesson) {
                /** @var VisitLesson $lesson */
                if ($lesson->lessonId == $lessonId) {
                    $lesson->status = $status;
                }

                $newLessons[] = (string)$lesson;
            }
            $visit->setLessons('['.(implode(",", $newLessons)).']');
            $this->visitRepository->save($visit);
        }
    }
}