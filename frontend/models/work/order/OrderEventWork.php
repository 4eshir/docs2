<?php

namespace app\models\work\order;

use common\models\scaffold\OrderMain;

class OrderEventWork extends OrderMain
{
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
    //
    public $scanFile;
    public $docFiles;
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'minister' => ''
        ]);
    }
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['scanFile'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag, txt'],
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10,
                'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag, txt']
        ]);
    }
}