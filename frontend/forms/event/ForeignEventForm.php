<?php

namespace frontend\forms\event;

use app\models\work\event\ForeignEventWork;
use app\models\work\order\OrderEventWork;
use app\models\work\team\ActParticipantWork;
use common\Model;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\event\ParticipantAchievementRepository;
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
    public $minister;
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

    public function __construct($foreignEventId, $config = [])
    {
        parent::__construct($config);
        /** @var OrderEventWork $order */
        /** @var ForeignEventWork $event */
        $event = (Yii::createObject(ForeignEventRepository::class))->get($foreignEventId);
        $order = (Yii::createObject(OrderEventRepository::class))->get($event->order_participant_id);
        $this->name = $order->order_name;
        $this->organizer = $event->organizer_id;
        $this->startDate = $event->begin_date;
        $this->endDate = $event->end_date;
        $this->city = $event->city;
        $this->format = $event->format;
        $this->level = $event->level;
        $this->minister = $event->minister;
        $this->minAge = $event->min_age;
        $this->maxAge = $event->max_age;
        $this->orderParticipant = $event->order_participant_id;
        $this->acts = $this->fillActParticipants($foreignEventId);

        $this->addOrderParticipant = $event->add_order_participant_id;
        $this->keyWords = $event->key_words;
        $this->escort = $event->escort_id;
        $this->orderBusinessTrip = $event->order_business_trip_id;
        $this->achievements = $this->fillAchievements($foreignEventId);
    }

    public function fillActParticipants($foreignEventId)
    {
        return (Yii::createObject(ActParticipantRepository::class))->getByForeignEventId($foreignEventId);
    }

    public function fillAchievements($foreignEventId)
    {
        return (Yii::createObject(ParticipantAchievementRepository::class))->getByForeignEvent($foreignEventId);
    }
}