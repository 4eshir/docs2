<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\base\Model;

class TrainingGroupScheduleForm extends Model
{
    use EventTrait;

    const MANUAL = 0;
    const AUTO = 1;

    public $id;
    public $trainingGroup;
    public $trainingProgram;
    public $number;
    public $type;
    public $lessons;
    public $prevLessons;

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id !== -1) {
            $this->lessons = (Yii::createObject(TrainingGroupRepository::class))->getLessons($id);
            $this->prevLessons = (Yii::createObject(TrainingGroupRepository::class))->getLessons($id);
            $this->trainingGroup = (Yii::createObject(TrainingGroupRepository::class))->get($id);
            $this->number = $this->trainingGroup->number;
            $this->trainingProgram = (Yii::createObject(TrainingProgramRepository::class))->get($this->trainingGroup->training_program_id);
            $this->id = $id;
        }
    }

    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['lessons', 'prevLessons', 'id', 'number', 'trainingProgram'], 'safe']
        ];
    }

    public function isManual()
    {
        return $this->type == self::MANUAL;
    }
}