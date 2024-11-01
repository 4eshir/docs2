<?php
namespace frontend\forms;
use common\events\EventTrait;
use common\models\scaffold\People;
use yii\base\Model;

class OrderEventForm extends Model {

    use EventTrait;

    public $id;
    public $order_copy_id;
    public $order_number;
    public $order_postfix;
    public $order_date;
    public $order_name;
    public $signed_id;
    public $bring_id;
    public $executor_id;
    public $key_words;
    public $creator_id;
    public $last_edit_id;
    public $target;
    public $type;
    public $state;
    public $nomenclature_id;
    public $study_type;

    // карточка участника
    public $eventName;
    public $organizer_id;
    public $dateBegin;
    public $dateEnd;
    public $city;
    public $minister;
    public $minAge;
    public $maxAge;
    public $eventWay;
    public $eventLevel;
    public $keyEventWords;
    //
    public $responsible_id;

    //Дополнительная информация для генерации приказа
    public $purpose;
    public $docEvent;
    public $respPeopleInfo;
    public $timeProvisionDay;
    public $extraRespInsert;
    public $timeInsertDay;
    public $extraRespMethod;
    public $extraRespInfoStuff;

    //награды и номинации
    public $team;
    public $award;
    public $teams;
    public $awards;
    public $participant_id;
    public $branch;
    public $teacher_id;
    public $focus;
    public $formRealization;
    public $teamList;
    public $nominationList;
    //

    public $scanFile;
    public $docFiles;
    public $actFiles;
    public function rules()
    {
        return [
            [['order_date'], 'required'],
            [['order_copy_id', 'order_postfix', 'signed_id', 'bring_id', 'executor_id',  'creator_id', 'last_edit_id',
                'nomenclature_id', 'type', 'state', 'organizer_id' , 'eventWay','eventLevel' ,'minister','minAge', 'maxAge' ,
                'purpose' ,'docEvent', 'respPeopleInfo', 'timeProvisionDay', 'extraRespInsert', 'timeInsertDay', 'extraRespMethod', 'extraRespInfoStuff'], 'integer'],
            [['order_date'], 'safe'],
            [['order_number', 'order_name'], 'string', 'max' => 64],
            [['key_words', 'keyEventWords'], 'string', 'max' => 512],
            [['eventName' ,'dateBegin', 'dateEnd', 'city'], 'string'],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['signed_id' => 'id']],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['last_edit_id' => 'id']],
        ];
    }
    public static function fill()
    {
        $entity = new static();
        return $entity;
    }
}