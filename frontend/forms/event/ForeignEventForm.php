<?php

namespace frontend\forms\event;

use app\models\work\order\OrderEventWork;
use app\models\work\team\ActParticipantWork;
use common\Model;
use common\repositories\order\OrderEventRepository;
use frontend\models\work\event\ParticipantAchievementWork;
use Yii;

class ForeignEventForm extends Model
{
    // Неизменяемые поля
    public $name;
    public $organizer;
    public $startDate;
    public $endDate;
    public $city;
    public $format;
    public $level;
    public $minpros;
    public $minAge;
    public $maxAge;
    public $orderParticipant;

    /** @var ActParticipantWork[] $acts */
    public array $acts;

    // Изменяемые поля
    public $addOrderParticipant;
    public $keyWords;
    public $doc;
    public $escort;
    public $orderBusinessTrip;

    /** @var ParticipantAchievementWork[] $achievements */
    public array $achievements;

    public function __construct($orderParticipantId, $config = [])
    {
        parent::__construct($config);
        /** @var OrderEventWork $order */
        $order = (Yii::createObject(OrderEventRepository::class))->get($orderParticipantId);
        $this->name = $order->order_name;
        $this->organizer = $order->or
    }
}