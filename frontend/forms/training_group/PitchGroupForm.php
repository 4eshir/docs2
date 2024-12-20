<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\Model;
use common\repositories\educational\GroupProjectThemesRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\educational\training_group\GroupProjectsThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\ProjectThemeWork;
use Yii;

class PitchGroupForm extends Model
{
    use EventTrait;

    public $id;
    public $number;
    public $experts;
    public $prevExperts;

    public $themes;
    public $prevThemes;
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
            $this->prevExperts = (Yii::createObject(TrainingGroupRepository::class))->getExperts($id) ?: [new TrainingGroupExpertWork];
            $this->themes = (Yii::createObject(TrainingGroupRepository::class))->getThemes($id) ?: [new ProjectThemeWork];
            $this->prevThemes = (Yii::createObject(GroupProjectThemesRepository::class))->getProjectThemesFromGroup($id) ?: [new GroupProjectsThemesWork];
        }
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['protectionDate', 'safe']
        ]);
    }
}