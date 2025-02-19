<?php

namespace backend\services\report;

use backend\forms\report\ManHoursReportForm;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use InvalidArgumentException;
use yii\helpers\ArrayHelper;

class ReportManHoursService
{
    private TrainingGroupRepository $groupRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private TrainingGroupLessonRepository $lessonRepository;
    private TeacherGroupRepository $teacherGroupRepository;
    private LessonThemeRepository $lessonThemeRepository;
    private VisitRepository $visitRepository;

    public function __construct(
        TrainingGroupRepository $groupRepository,
        TrainingGroupParticipantRepository $participantRepository,
        TrainingGroupLessonRepository $lessonRepository,
        TeacherGroupRepository $teacherGroupRepository,
        LessonThemeRepository $lessonThemeRepository,
        VisitRepository $visitRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->participantRepository = $participantRepository;
        $this->lessonRepository = $lessonRepository;
        $this->teacherGroupRepository = $teacherGroupRepository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->visitRepository = $visitRepository;
    }

    /**
     * Метод подсчета человеко-часов за заданный период и с заданным типом подсчета
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $calculateType
     * @param int[] $teacherIds
     * @return int
     */
    public function calculateManHours(string $startDate, string $endDate, int $calculateType, array $teacherIds = []) : int
    {
        $teacherLessonIds = [];
        if (count($teacherIds) > 0) {
            $teacherLessonIds = ArrayHelper::getColumn(
                $this->lessonThemeRepository->getByTeacherIds($teacherIds),
                'training_group_lesson_id'
            );
        }

        $participants = $this->participantRepository->getParticipantsFromGroups(
            ArrayHelper::getColumn($this->groupRepository->getBetweenDates($startDate, $endDate), 'id')
        );

        $visits = $this->visitRepository->getParticipantsFromGroup(
            ArrayHelper::getColumn($participants, 'id')
        );

        $result = 0;
        foreach ($visits as $visit) {
            /** @var VisitWork $visit */
            $lessons = VisitLesson::fromString($visit->lessons);
            foreach ($lessons as $lesson) {
                $result += $this->checkVisitLesson($lesson, $calculateType, $teacherLessonIds);
            }
        }

        return $result;
    }

    /**
     * Вспомогательная функция проверки учета занятия в отчете по человеко-часам
     *
     * @param VisitLesson $visitLesson
     * @param int $calculateType
     * @param int[] $teacherIds
     * @return int
     */
    private function checkVisitLesson(VisitLesson $visitLesson, int $calculateType, array $teacherLessonIds = [])
    {
        $conditionTeacher = true;
        if (count($teacherLessonIds) > 0) {
            $conditionTeacher = in_array($visitLesson->lessonId, $teacherLessonIds);
        }

        if (
            ($visitLesson->status == VisitWork::ATTENDANCE || $visitLesson->status == VisitWork::DISTANCE) ||
            ($calculateType == ManHoursReportForm::MAN_HOURS_ALL && $visitLesson->status == VisitWork::NO_ATTENDANCE) &&
            $conditionTeacher
        ) {
            return 1;
        }

        return 0;
    }

    /**
     * Метод подсчета обучающихся за заданный период и заданным типом/подтипом подсчета
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $calculateType тип поиск групп (время начала/окончания занятий)
     * @param int $calculateSubtype подтип для фильтрации обучающихся (уникальные/все)
     * @param int[] $teacherIds id преподавателей для фильтрации групп
     * @return int
     */
    public function calculateParticipantsByPeriod(
        string $startDate,
        string $endDate,
        int $calculateType,
        int $calculateSubtype,
        array $teacherIds = []
    ) : int
    {
        $filterGroupIds = [];
        if (count($teacherIds) > 0) {
            $filterGroupIds = ArrayHelper::getColumn(
                $this->teacherGroupRepository->getAllFromTeacherIds($teacherIds),
                'training_group_id'
            );
        }

        switch ($calculateType) {
            case ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN:
                $groups = $this->groupRepository->getStartBeforeFinishInDates($startDate, $endDate, $filterGroupIds);
                break;
            case ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER:
                $groups = $this->groupRepository->getStartInFinishAfterDates($startDate, $endDate, $filterGroupIds);
                break;
            case ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN:
                $groups = $this->groupRepository->getStartInFinishInDates($startDate, $endDate, $filterGroupIds);
                break;
            case ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER:
                $groups = $this->groupRepository->getStartBeforeFinishAfterDates($startDate, $endDate, $filterGroupIds);
                break;
            default:
                throw new InvalidArgumentException('Неизвестный тип периода');
        }

        $participants = $this->participantRepository->getParticipantsFromGroups(
            ArrayHelper::getColumn($groups, 'id')
        );

        switch ($calculateSubtype) {
            case ManHoursReportForm::PARTICIPANTS_ALL:
                return count($participants);
            case ManHoursReportForm::PARTICIPANTS_UNIQUE:
                return count(
                    array_unique(
                        ArrayHelper::getColumn(
                            $participants,
                            'participant_id'
                        )
                    )
                );
            default:
                return -1;
        }
    }

    public function generateManHoursReport()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        //--Основные отчетные данные--
        //Ожидается массив, если -1 - значит соответствующий пункт не выбран
        $gp1 = -1;
        $gp2 = -1;
        $gp3 = -1;
        $gp4 = -1;

        $groups1Id = [];
        $groups2Id = [];
        $groups3Id = [];
        $groups4Id = [];

        $groupParticipants1 = [];
        $groupParticipants2 = [];
        $groupParticipants3 = [];
        $groupParticipants4 = [];
        //----------------------------

        $debugCSV = "Группа;Кол-во занятий выбранного педагога;Кол-во занятий всех педагогов;Кол-во учеников;Кол-во ч/ч\r\n";
        $debugCSV2 = "ФИО обучающегося;Группа;Дата начала занятий;Дата окончания занятий;Отдел;Пол;Дата рождения;Направленность;Педагог;Основа;Тематическое направление;Образовательная программа;Форма реализации;Успешное завершение;Тема проекта;Дата защиты;Тип проекта;ФИО эксперта;Тип эксперта;Место работы эксперта;Должность эксперта;Раздел\r\n";


        $mainHeader = "<b>Отчет по</b><br>";
        $firstHeader = '';
        $secondHeader = '';


        foreach ($this->type as $oneType)
        {

            if ($oneType == '0')
            {
                if ($firstHeader == '') $firstHeader = "человеко-часам<br>";

                //--ОТЧЕТ ПО ЧЕЛОВЕКО-ЧАСАМ--

                //--Основной алгоритм--

                $groups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD,
                    $this->start_date, $this->end_date,
                    $this->branch,
                    $this->focus,
                    $this->allow_remote,
                    $this->budget,
                    $this->teacher == '' ? [] : $this->teacher);


                $participants = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $groups, 0, ReportConst::AGES_ALL, date('Y-m-d'));

                $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $participants, $this->start_date, $this->end_date, $this->method == 0 ? VisitWork::ONLY_PRESENCE : VisitWork::PRESENCE_AND_ABSENCE/*, $this->teacher == null ? [] : [$this->teacher]*/);

                //---------------------


                //--Отладочная информация--

                $debugManHours = DebugReportFunctions::DebugDataManHours($groups,
                    $this->start_date, $this->end_date,
                    $this->method == 0 ? VisitWork::ONLY_PRESENCE : VisitWork::PRESENCE_AND_ABSENCE,
                    $this->teacher == '' ? [] : $this->teacher);



                foreach ($debugManHours as $one)
                    $debugCSV .= $one->group.";".
                        count($one->lessonsChangeTeacher).";".
                        count($one->lessonsAll).";".
                        count($one->participants).";".
                        count($one->manHours)."\r\n";

                //-------------------------

                $resultManHours = $this->generateView($visits, ManHoursReportForm::MAN_HOURS_REPORT);

                //---------------------------
            }
            else
            {
                if ($secondHeader == '') $secondHeader = "обучающимся<br>";

                //--ОТЧЕТ ПО КОЛИЧЕСТВУ ОБУЧАЮЩИХСЯ--

                //--Основной алгоритм--

                if ($oneType == '1')
                {
                    $groups1 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_EARLY_END_IN]);

                    $groups1Id = SupportReportFunctions::GetIdFromArray($groups1);


                    $groupParticipants1 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups1Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups1Id])->all();

                    $gp1 = count($groupParticipants1);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(1, $groupParticipants1, $this->unic, SupportReportFunctions::GetIdFromArray($groups1));

                }

                if ($oneType == '2')
                {
                    $groups2 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_IN_END_LATER]);

                    $groups2Id = SupportReportFunctions::GetIdFromArray($groups2);

                    $groupParticipants2 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups2Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups2Id])->all();

                    $gp2 = count($groupParticipants2);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(2, $groupParticipants2, $this->unic, SupportReportFunctions::GetIdFromArray($groups2));

                }

                if ($oneType == '3')
                {
                    $groups3 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_IN_END_IN]);

                    $groups3Id = SupportReportFunctions::GetIdFromArray($groups3);

                    $groupParticipants3 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups3Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups3Id])->all();

                    $gp3 = count($groupParticipants3);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(3, $groupParticipants3, $this->unic, SupportReportFunctions::GetIdFromArray($groups3));

                }

                if ($oneType == '4')
                {
                    $groups4 = SupportReportFunctions::GetTrainingGroups(
                        ReportConst::PROD,
                        $this->start_date, $this->end_date,
                        $this->branch,
                        $this->focus,
                        $this->allow_remote,
                        $this->budget,
                        [],
                        [ReportConst::START_EARLY_END_LATER]);

                    $groups4Id = SupportReportFunctions::GetIdFromArray($groups4);

                    $groupParticipants4 = $this->unic == 0 ?
                        TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groups4Id])->all() :
                        TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $groups4Id])->all();

                    $gp4 = count($groupParticipants4);

                    if ($this->unic == 0)
                        $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(4, $groupParticipants4, $this->unic, SupportReportFunctions::GetIdFromArray($groups4));

                }

                //---------------------

                if ($this->unic == 0)
                    $resultParticipantCount = $this->generateView([$gp1, $gp2, $gp3, $gp4], ManHoursReportForm::PARTICIPANTS_REPORT);

                //-----------------------------------
            }
        }


        //--Отладочная информация--


        if ($this->unic == 1)
        {
            $allGroups = array_merge($groups1Id, array_merge($groups2Id, array_merge($groups3Id, $groups4Id)));

            $allParticipants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->where(['IN', 'training_group_id', $allGroups])->all();

            $debugCSV2 .= DebugReportFunctions::DebugDataParticipantsCount(0, $allParticipants, $this->unic, $allGroups);

            $resultParticipantCount = $this->generateView($allParticipants, ManHoursReportForm::PARTICIPANTS_UNIQUE_REPORT);
        }

        //-------------------------


        $result = '<table class="table table-bordered">';

        $result .= $resultManHours;
        $result .= $resultParticipantCount;

        $result .= '</table>';



        return [$mainHeader.$firstHeader.$secondHeader, $result, $debugCSV, $debugCSV2];
    }
}