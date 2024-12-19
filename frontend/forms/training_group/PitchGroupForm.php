<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\Model;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\ProjectThemeWork;
use Yii;

class PitchGroupForm extends Model
{
    use EventTrait;

    public $id;
    public $number;
    public $experts;

    public $themes;
    public $protectionDate;
    public $themeIds;

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id != -1) {
            $this->id = $id;
            $this->number = (Yii::createObject(TrainingGroupRepository::class))->get($id)->number;
            $this->protectionDate = (Yii::createObject(TrainingGroupRepository::class))->get($id)->protection_date;
            $this->experts = (Yii::createObject(TrainingGroupRepository::class))->getExperts($id) ?: [new TrainingGroupExpertWork];
            $this->themes = (Yii::createObject(TrainingGroupRepository::class))->getThemes($id) ?: [new ProjectThemeWork];
        }
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['protectionDate', 'safe']
        ]);
    }
}