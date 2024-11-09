<?php
namespace frontend\forms;
use app\models\work\event\ForeignEventWork;
use app\models\work\order\OrderEventWork;
use common\events\EventTrait;
use common\models\scaffold\People;
use yii\base\Model;

class OrderEventForm extends Model {
    public $isNewRecord;
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

    // карточка мероприятия
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
    public $participant_personal_id;
    public $branch;
    public $teacher_id;
    public $teacher2_id;
    public $focus;
    public $formRealization;
    public $teamList;
    public $nominationList;
    //
    public $typeActParticipant;
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
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt']
        ];
    }
    public function attributeLabels(){
        return array_merge(parent::attributeLabels(), [
            'typeActParticipant' => 'Личный тип участия'
        ]);
    }
    public static function fill(
        OrderEventWork $modelOrderEvent,
        ForeignEventWork $foreignEvent
    )
    {
        $entity = new static();
        $entity->order_copy_id = $modelOrderEvent->order_copy_id;
        $entity->order_number = $modelOrderEvent->order_number;
        $entity->order_postfix = $modelOrderEvent->order_postfix;
        $entity->order_date = $modelOrderEvent->order_date;
        $entity->order_name = $modelOrderEvent->order_name;
        $entity->signed_id = $modelOrderEvent->signed_id;
        $entity->bring_id = $modelOrderEvent->bring_id;
        $entity->executor_id = $modelOrderEvent->executor_id;
        $entity->key_words = $modelOrderEvent->key_words;
        $entity->creator_id = $modelOrderEvent->creator_id;
        $entity->last_edit_id = $modelOrderEvent->last_edit_id;
        //$entity->target = $modelOrderEvent->target;
        $entity->type = $modelOrderEvent->type;
        $entity->state = $modelOrderEvent->state;
        $entity->nomenclature_id = $modelOrderEvent->nomenclature_id;
        $entity->study_type = $modelOrderEvent->study_type;
// карточка мероприятия
        $entity->eventName = $foreignEvent->name;
        $entity->organizer_id = $foreignEvent->organizer_id;
        $entity->dateBegin = $foreignEvent->begin_date;
        $entity->dateEnd = $foreignEvent->end_date;
        $entity->city = $foreignEvent->city;
        $entity->minister = $foreignEvent->minister;
        $entity->minAge = $foreignEvent->min_age;
        $entity->maxAge = $foreignEvent->max_age;
        $entity->eventWay = $foreignEvent->format;
        $entity->eventLevel = $foreignEvent->level;
        $entity->keyEventWords = $foreignEvent->key_words;
// Дополнительная информация для генерации приказа
        /*
        $entity->purpose;
        $entity->docEvent;
        $entity->respPeopleInfo;
        $entity->timeProvisionDay;
        $entity->extraRespInsert;
        $entity->timeInsertDay;
        $entity->extraRespMethod;
        $entity->extraRespInfoStuff;
// награды и номинации
        $entity->team;
        $entity->award;
        $entity->teams;
        $entity->awards;
        $entity->participant_id;
        $entity->branch;
        $entity->teacher_id;
        $entity->teacher2_id;
        $entity->focus;
        $entity->formRealization;
        $entity->teamList;
        $entity->nominationList;

        $entity->scanFile;
        $entity->docFiles;
        $entity->actFiles;
        */
        return $entity;
    }
}