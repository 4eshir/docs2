<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\order\OrderTrainingRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\GroupProjectThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\order\OrderTrainingWork;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @property TrainingProgramWork $trainingProgram
 * @property TrainingGroupWork $trainingGroup
 * @property OrderTrainingWork $orders
 * @property array $teachers
 */

class TrainingGroupCombinedForm extends Model
{
    use EventTrait;

    public $id;                 // учебной группы
    public $number;             // номер группы
    public $trainingGroup;      // учебная группа

    public $trainingProgram;    // образовательная программа
    public $teachers;           // педагоги
    public $orders;             // приказы группы


    public $photoFiles;
    public $presentationFiles;
    public $workMaterialFiles;


    // ----------------------------

    // Информация об учениках группы
    public $participants;
    // -----------------------------

    // Информация о занятиях группы
    public $lessons;
    // -----------------------------

    // Информация о защитах
    public $protectionDate;
    public $themes;
    public $experts;

    // -----------------------------

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id !== -1) {
            /** @var TrainingGroupWork $model */
            $model = (Yii::createObject(TrainingGroupRepository::class))->get($id);
            $this->fillBaseInfo($model);
            $this->fillParticipantsInfo($model);
            $this->fillLessonsInfo($model);
            $this->fillPitchInfo($model);
        }
    }

    private function fillBaseInfo(TrainingGroupWork $model)
    {
        $this->id = $model->id;
        $this->number = $model->number;
        $this->trainingGroup = $model;
        $this->trainingProgram = (Yii::createObject(TrainingProgramRepository::class))->get($model->training_program_id);
        $this->orders = (Yii::createObject(OrderTrainingRepository::class))->getAllByGroup($this->id);

        $this->teachers = (Yii::createObject(TeacherGroupRepository::class))->getAllTeachersFromGroup($model->id);

        $this->photoFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PHOTO), 'link'));
        $this->presentationFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PRESENTATION), 'link'));
        $this->workMaterialFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_WORK), 'link'));
    }

    private function fillParticipantsInfo(TrainingGroupWork $model)
    {
        $this->participants = (Yii::createObject(TrainingGroupParticipantRepository::class))->getParticipantsFromGroups([$model->id]);
    }

    private function fillLessonsInfo(TrainingGroupWork $model)
    {
        $this->lessons = (Yii::createObject(TrainingGroupLessonRepository::class))->getLessonsFromGroup($model->id);
    }

    private function fillPitchInfo(TrainingGroupWork $model)
    {
        $this->protectionDate = $model->protection_date;
        $this->themes = (Yii::createObject(GroupProjectThemesRepository::class))->getProjectThemesFromGroup($model->id);
        $this->experts = (Yii::createObject(TrainingGroupExpertRepository::class))->getExpertsFromGroup($model->id);
    }

    public function getPrettyParticipants()
    {
        $result = [];
        if (is_array($this->participants)) {
            foreach ($this->participants as $participant) {
                /** @var TrainingGroupParticipantWork $participant */
                $result[] = StringFormatter::stringAsLink(
                    $participant->participantWork->getFIO(ForeignEventParticipantsWork::FIO_FULL),
                    Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $participant->participant_id])
                );
            }
        }

        return $result;
    }

    public function getPrettyLessons()
    {
        $result = [];
        if (is_array($this->lessons)) {
            foreach ($this->lessons as $lesson) {
                /** @var TrainingGroupLessonWork $lesson */
                $date = DateFormatter::format($lesson->lesson_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
                $result[] = "$date с $lesson->lesson_start_time до $lesson->lesson_end_time в ауд. {$lesson->auditoriumWork->name}";
            }
        }
        $result = implode('<br>', $result);
        return HtmlBuilder::createAccordion($result);
    }

    public function getPrettyThemes()
    {
        $result = [];
        if (is_array($this->themes)) {
            foreach ($this->themes as $theme) {
                /** @var GroupProjectThemesWork $theme */
                $type = Yii::$app->projectType->get($theme->projectThemeWork->project_type);
                $result[] = "{$theme->projectThemeWork->name} ($type проект)";
            }
        }

        return $result;
    }

    public function getPrettyExperts()
    {
        $result = [];
        if (is_array($this->experts)) {
            foreach ($this->experts as $expert) {
                /** @var TrainingGroupExpertWork $expert */
                $result[] = "({$expert->getExpertTypeString()}) {$expert->expertWork->getFIO(PeopleWork::FIO_WITH_POSITION)}";
            }
        }

        return $result;
    }

    /**
     * Текстовое представление отдела учебной группы
     * @return mixed|string|null
     */
    public function getBranch()
    {
        return $this->trainingGroup->branch ? Yii::$app->branches->get($this->trainingGroup->branch) : '---';
    }

    /**
     * Ссылки на преподов учебной группы
     * @return mixed
     */
    public function getTeachersRaw()
    {
        return $this->trainingGroup->getTeachersList(StringFormatter::FORMAT_LINK);
    }

    /**
     * Период обучения группы
     * @return string
     */
    public function getTrainingPeriod()
    {
        return DateFormatter::format($this->trainingGroup->start_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot)
            . ' - '
            . DateFormatter::format($this->trainingGroup->finish_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
    }

    /**
     * Ссылка на образовательную программу
     * @return string
     */
    public function getTrainingProgramRaw()
    {
        return $this->trainingProgram ?
            StringFormatter::stringAsLink(
                $this->trainingProgram->name,
                Url::to([Yii::$app->frontUrls::PROGRAM_VIEW, 'id' => $this->trainingProgram->id])
            ) : '---';
    }

    /**
     * Форма обучения бюджет/внебюджет и сетевая/несетевая форма
     * @return string
     */
    public function getFormStudy()
    {
        $budget = $this->trainingGroup->budget == 1 ? 'Бюджет' : 'Внебюджет';
        $network = $this->trainingGroup->is_network == 1 ? 'сетевая' : 'несетевая';
        return $budget . ' ' . $network;
    }

    /**
     * Информация о загрузке приказов
     * @return string
     */
    public function getConsentOrders()
    {
        if ($this->trainingGroup->archive == 0 && $this->trainingGroup->order_stop == 0) {
            return 'Разрешена';
        }
        return 'Запрещена';
    }

    /**
     * Ссылки на все приказы в которых фигурируют дети из учебной группы
     * @return false|string
     */
    public function getOrdersRaw()
    {
        $result = '';
        if (count($this->orders) == 0) {
            return '---';
        }

        foreach ($this->orders as $order)
        {
            $result .= StringFormatter::stringAsLink(
                'Приказ № '.$order->getFullName(),
                Url::to([Yii::$app->frontUrls::ORDER_TRAINING_VIEW, 'id' => $order->id])
            ) . '<br>';
        }

        return substr($result, 0, -4);
    }

    public function getManHoursPercent()
    {
        /*
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all();
        $lessonsId = [];
        foreach ($lessons as $lesson)
            $lessonsId[] = $lesson->id;
        $visits = count(VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['status' => 0])->all()) + count(VisitWork::find()->where(['IN', 'training_group_lesson_id', $lessonsId])->andWhere(['status' => 2])->all());
        $maximum = count(TrainingGroupParticipantWork::find()->where(['training_group_id' => $this->id])->all()) * count(TrainingGroupLessonWork::find()->where(['training_group_id' => $this->id])->all());
        $percent = (($visits * 1.0) / ($maximum * 1.0)) * 100;
        $numbPercent = $percent;
        $percent = round($percent, 2);
        if ($numbPercent > 75.0)
            $percent = '<p style="color: #1e721e; display: inline">'.$percent;
        else if ($numbPercent > 50.0)
            $percent = '<p style="color: #d49939; display: inline">' .$percent;
        else
            $percent = '<p style="color: #c34444; display: inline">' .$percent;
            $percent = '<p style="color: #c34444; display: inline">' .$percent;
        $result = $visits.' / '.$maximum.' (<b>'.$percent.'%</b></p>)';
        return $result;
         */
    }

    /**
     * Количество детей в группе
     * @return string
     */
    public function getCountParticipants()
    {
        return count($this->participants) . ' (включая отчисленных и переведенных)';
    }

    /**
     * Количество занятий в расписании
     * @return string
     */
    public function getCountLessons()
    {
        return count($this->lessons) . '  академ.часа';
    }

    /**
     * Создаьедб учебной группы
     * @return string
     */
    public function getCreatorName()
    {
        $creator = $this->trainingGroup->creatorWork;
        return $creator ? $creator->getFullName() : '---';
    }

    /**
     * Последний редактор учебной группы
     * @return string
     */
    public function getLastEditorName()
    {
        $editor = $this->trainingGroup->lastEditorWork;
        return $editor ? $editor->getFullName() : '---';
    }
}