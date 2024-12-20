<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\ProjectThemeRepository;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupExpertRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\PeopleStampRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\GroupProjectsThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\ProjectThemeWork;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @property TrainingProgramWork $trainingProgram
 * @property array $teachers
 */

class TrainingGroupCombinedForm extends Model
{
    use EventTrait;

    // Основная информация о группе
    public $number;
    public $branch;
    public $budget;
    public $trainingProgram;
    public $network;
    public $teachers;
    public $endLoadOrders;
    public $startDate;
    public $endDate;
    public $photoFiles;
    public $presentationFiles;
    public $workMaterialFiles;

    public $id;
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
        $this->branch = $model->branch;
        $this->budget = $model->budget;
        $this->trainingProgram = (Yii::createObject(TrainingProgramRepository::class))->get($model->training_program_id);
        $this->network = $model->is_network;
        $this->teachers = (Yii::createObject(TeacherGroupRepository::class))->getAllTeachersFromGroup($model->id);
        $this->endLoadOrders = $model->order_stop;
        $this->startDate = $model->start_date;
        $this->endDate = $model->finish_date;
        $this->photoFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PHOTO), 'link'));
        $this->presentationFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PRESENTATION), 'link'));
        $this->workMaterialFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_WORK), 'link'));
    }

    private function fillParticipantsInfo(TrainingGroupWork $model)
    {
        $this->participants = (Yii::createObject(TrainingGroupParticipantRepository::class))->getParticipantsFromGroup($model->id);
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

        return $result;
    }

    public function getPrettyThemes()
    {
        $result = [];
        if (is_array($this->themes)) {
            foreach ($this->themes as $theme) {
                /** @var GroupProjectsThemesWork $theme */
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
}