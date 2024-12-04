<?php
namespace app\models\work\event;
use common\events\EventTrait;
use common\helpers\DateFormatter;
use common\models\scaffold\ForeignEvent;

class ForeignEventWork extends ForeignEvent
{
    use EventTrait;
    public $actFiles;

    public static function fill(
        $name,
        $organizerId,
        $beginDate, $endDate,
        $city,
        $format, $level,
        $minister,
        $minAge, $maxAge,
        $keyWords,
        $orderParticipantId,
        $actFiles
    )
    {
        $entity = new static();
        $entity->name = $name;
        $entity->organizer_id = $organizerId;
        $entity->begin_date = $beginDate;
        $entity->end_date = $endDate;
        $entity->city = $city;
        $entity->format = $format;
        $entity->level = $level;
        $entity->minister = $minister;
        $entity->min_age = $minAge;
        $entity->max_age = $maxAge;
        $entity->key_words = $keyWords;
        $entity->order_participant_id = $orderParticipantId;
        $entity->actFiles = $actFiles;
        return $entity;
    }
    public function fillUpdate(
        $name,
        $organizerId,
        $beginDate, $endDate,
        $city,
        $format, $level,
        $minister,
        $minAge, $maxAge,
        $keyWords,
        $orderParticipantId,
        $actFiles
    )
    {
        $this->name = $name;
        $this->organizer_id = $organizerId;
        $this->begin_date = $beginDate;
        $this->end_date = $endDate;
        $this->city = $city;
        $this->format = $format;
        $this->level = $level;
        $this->minister = $minister;
        $this->min_age = $minAge;
        $this->max_age = $maxAge;
        $this->key_words = $keyWords;
        $this->order_participant_id = $orderParticipantId;
        $this->actFiles = $actFiles;
    }
    public function beforeValidate()
    {
        $this->begin_date = DateFormatter::format($this->begin_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        $this->end_date = DateFormatter::format($this->end_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
        return parent::beforeValidate(); 
    }


}