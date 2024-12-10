<?php

namespace frontend\forms\training_group;

use common\events\EventTrait;
use common\repositories\educational\TrainingGroupRepository;
use Yii;
use yii\base\Model;

class TrainingGroupParticipantForm extends Model
{
    use EventTrait;

    public $id;
    public $number;
    public $participants;
    public $prevParticipants;

    public function __construct($id = -1, $config = [])
    {
        parent::__construct($config);
        if ($id !== -1) {
            $this->participants = (Yii::createObject(TrainingGroupRepository::class))->getParticipants($id);
            $this->prevParticipants = (Yii::createObject(TrainingGroupRepository::class))->getParticipants($id);
            $this->number = (Yii::createObject(TrainingGroupRepository::class))->get($id)->number;
            $this->id = $id;
        }
    }

    public function rules()
    {
        return [
            [['participants'], 'safe']
        ];
    }
}