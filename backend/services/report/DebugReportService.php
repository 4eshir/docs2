<?php

namespace backend\services\report;

use backend\helpers\DebugReportHelper;
use backend\helpers\ReportHelper;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\helpers\ArrayHelper;

class DebugReportService
{
    private TrainingGroupRepository $groupRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private VisitRepository $visitRepository;

    public function __construct(
        TrainingGroupRepository $groupRepository,
        LessonThemeRepository $lessonThemeRepository,
        TrainingGroupParticipantRepository $participantRepository,
        VisitRepository $visitRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->participantRepository = $participantRepository;
        $this->visitRepository = $visitRepository;
    }

    /**
     * @param TrainingGroupParticipantWork[] $participants
     * @return string[][]
     */
    public function createParticipantDebugData(array $participants): array
    {
        $data = [];
        foreach ($participants as $participant) {
            $data[] = DebugReportHelper::createParticipantsDataCsv($participant);
        }

        return $data;
    }

    /**
     * @param TrainingGroupWork[] $groups
     * @param int $calculateType
     * @param int[] $teacherIds
     * @return string[][]
     */
    public function createManHoursDebugData(array $groups, int $calculateType, array $teacherIds = []) : array
    {
        $data = [];
        foreach ($groups as $group) {
            $lessons = $this->groupRepository->getLessons($group->id);
            $allLessons = $this->lessonThemeRepository->getByLessonIds(ArrayHelper::getColumn($lessons, 'id'));
            $teacherLesson = $this->lessonThemeRepository->getByTeacherIds($teacherIds);

            $visits = $this->visitRepository->getByTrainingGroup($group->id);
            $visitsCount = 0;
            foreach ($visits as $visit) {
                /** @var VisitWork $visit */
                $lessons = VisitLesson::fromString($visit->lessons);
                foreach ($lessons as $lesson) {
                    $visitsCount += ReportHelper::checkVisitLesson($lesson, $calculateType, ArrayHelper::getColumn($teacherLesson, 'id'));
                }
            }

            $data[] = [
                $group->number,
                count($teacherLesson) > 0 ?: count($allLessons),
                count($allLessons),
                count($this->participantRepository->getParticipantsFromGroups([$group->id])),
                (string)$visitsCount,
            ];
        }

        return $data;
    }
}