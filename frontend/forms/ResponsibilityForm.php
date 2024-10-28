<?php

namespace frontend\forms;

use app\models\work\order\OrderMainWork;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\regulation\RegulationWork;
use yii\base\Model;

class ResponsibilityForm extends Model
{
    public $responsibilityType;
    public $branch;
    public $auditoriumId;
    public $quant;
    public $peopleStampId;
    public $startDate;
    public $endDate;
    public $orderId;
    public $regulationId;
    public $filesList;

    public function rules()
    {
        return [
            [['responsibilityType', 'branch', 'auditoriumId', 'quant', 'peopleId', 'regulationId', 'orderId'], 'integer'],
            [['auditoriumId'], 'exist', 'skipOnError' => true, 'targetClass' => AuditoriumWork::class, 'targetAttribute' => ['auditorium_id' => 'id']],
            [['peopleStampId'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStampWork::class, 'targetAttribute' => ['people_stamp_id' => 'id']],
            [['regulationId'], 'exist', 'skipOnError' => true, 'targetClass' => RegulationWork::class, 'targetAttribute' => ['regulation_id' => 'id']],
            [['orderId'], 'exist', 'skipOnError' => true, 'targetClass' => OrderMainWork::class, 'targetAttribute' => ['order_id' => 'id']],
            [['startDate', 'endDate'], 'safe'],
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10]
        ];
    }

    // Проверка на то, прикреплена ли ответственность к человеку
    public function isAttach()
    {
        return $this->peopleStampId !== null;
    }
}