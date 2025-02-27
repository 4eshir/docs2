<?php

namespace backend\services\report;

use backend\forms\report\ManHoursReportForm;
use backend\helpers\ReportHelper;
use backend\invokables\report\CheckVisitLesson;
use backend\repositories\report\TrainingGroupReportRepository;
use common\repositories\educational\LessonThemeRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ReportManHoursService
{
    private TrainingGroupReportRepository $repository;
    private LessonThemeRepository $lessonThemeRepository;
    private TrainingGroupParticipantRepository $participantRepository;
    private VisitRepository $visitRepository;

    private DebugReportService $debugService;

    public function __construct(
        TrainingGroupReportRepository      $repository,
        LessonThemeRepository              $lessonThemeRepository,
        TrainingGroupParticipantRepository $participantRepository,
        VisitRepository                    $visitRepository,
        DebugReportService                 $debugService
    )
    {
        $this->repository = $repository;
        $this->lessonThemeRepository = $lessonThemeRepository;
        $this->participantRepository = $participantRepository;
        $this->visitRepository = $visitRepository;
        $this->debugService = $debugService;
    }

    /**
     * Вспомогательная функция для генерации отчетов
     * Возвращает запрос на получение отфильтрованных групп
     *
     * @param array $branches
     * @param array $focuses
     * @param array $allowRemotes
     * @param array $budgets
     * @return ActiveQuery
     */
    private function getTrainingGroupsQueryByFilters(
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $budgets
    ) : ActiveQuery
    {
        $query = $this->repository->query();
        $query = $this->repository->filterGroupsByBranches($query, $branches);
        $query = $this->repository->filterGroupsByFocuses($query, $focuses);
        $query = $this->repository->filterGroupsByAllowRemote($query, $allowRemotes);
        return $this->repository->filterGroupsByBudget($query, $budgets);
    }

    /**
     * Метод подсчета человеко-часов за заданный период и с заданным типом подсчета
     *
     * @param string $startDate
     * @param string $endDate
     * @param int[] $branches
     * @param int[] $focuses
     * @param int[] $allowRemotes
     * @param int[] $budgets
     * @param int $calculateType
     * @param int[] $teacherIds передаются id из таблицы {@see PeopleStamp}, не из {@see People}
     * @return array
     */
    public function calculateManHours(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $budgets,
        int $calculateType,
        array $teacherIds = [],
        int $mode = ManHoursReportForm::MODE_PURE
    ) : array
    {
        $query = $this->getTrainingGroupsQueryByFilters($branches, $focuses, $allowRemotes, $budgets);

        $query = $this->repository->filterGroupsBetweenDates($query, $startDate, $endDate);
        $groups = $this->repository->findAll($query);


        $participants = $this->participantRepository->getParticipantsFromGroups(
            ArrayHelper::getColumn($groups, 'id')
        );

        $visits = $this->visitRepository->getByTrainingGroupParticipants(
            ArrayHelper::getColumn($participants, 'id')
        );

        $teacherLessonIds = ArrayHelper::getColumn(
            $this->lessonThemeRepository->getByTeacherIds($teacherIds),
            'training_group_lesson_id'
        );

        $result = 0;
        foreach ($visits as $visit) {
            /** @var VisitWork $visit */
            $lessons = VisitLesson::fromString($visit->lessons);
            foreach ($lessons as $lesson) {
                $result += ReportHelper::checkVisitLesson($lesson, $calculateType, $teacherLessonIds);
            }
        }

        return [
            'result' => $result,
            'debugData' => $mode == ManHoursReportForm::MODE_DEBUG ?
                $this->debugService->createManHoursDebugData($groups, $calculateType, $teacherIds) :
                ''
        ];
    }


    /**
     * Метод подсчета обучающихся за заданный период и заданным типом/подтипом подсчета
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $branches
     * @param array $focuses
     * @param array $allowRemotes
     * @param array $budgets
     * @param int[] $calculateTypes типы периодов для поиска групп
     * @param int $calculateSubtype подтип для фильтрации обучающихся (уникальные/все)
     * @param int[] $teacherIds передаются id из таблицы {@see PeopleStamp}, не из {@see People}
     * @return array
     */
    public function calculateParticipantsByPeriod(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $budgets,
        array $calculateTypes,
        int $calculateSubtype,
        array $teacherIds = [],
        int $mode = ManHoursReportForm::MODE_PURE
    ) : array
    {
        $query = $this->getTrainingGroupsQueryByFilters($branches, $focuses, $allowRemotes, $budgets);
        $query = $this->repository->filterGroupsByDates($query, $startDate, $endDate, $calculateTypes);

        $query = $this->repository->filterGroupsByTeachers($query, $teacherIds);
        $groups = $this->repository->findAll($query);

        $participants = $this->participantRepository->getParticipantsFromGroups(
            ArrayHelper::getColumn($groups, 'id')
        );

        if ($calculateSubtype === ManHoursReportForm::PARTICIPANTS_UNIQUE) {
            $uniqueParticipants = array_reduce($participants, function ($carry, $item) {
                $participantId = $item->participant_id;
                if (!isset($carry[$participantId])) {
                    $carry[$participantId] = $item;
                }
                return $carry;
            }, []);

            $participants = $this->participantRepository->getByIds(
                ArrayHelper::getColumn(
                    $uniqueParticipants,
                    'id'
                )
            );
        }

        return [
            'result' => count($participants),
            'debugData' => $mode == ManHoursReportForm::MODE_DEBUG ?
                $this->debugService->createParticipantDebugData($participants) :
                ''
        ];
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