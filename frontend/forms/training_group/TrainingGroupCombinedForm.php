<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\repositories\educational\TeacherGroupRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property TrainingProgramWork $trainingProgram
 * @property array $teachers
 */

class TrainingGroupCombinedForm extends Model
{
    use EventTrait;

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

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id !== -1) {
            /** @var TrainingGroupWork $model */
            $model = (Yii::createObject(TrainingGroupRepository::class))->get($id);
            $this->id = $model->id;
            $this->number = $model->number;
            $this->branch = $model->branch;
            $this->budget = $model->budget;
            $this->trainingProgram = (Yii::createObject(TrainingProgramRepository::class))->get($model->training_program_id);
            $this->network = $model->is_network;
            $this->teachers = (Yii::createObject(TeacherGroupRepository::class))->getAllTeachersFromGroup($id);
            $this->endLoadOrders = $model->order_stop;
            $this->startDate = $model->start_date;
            $this->endDate = $model->finish_date;
            $this->photoFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PHOTO), 'link'));
            $this->presentationFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PRESENTATION), 'link'));
            $this->workMaterialFiles = implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_WORK), 'link'));
        }
    }
}