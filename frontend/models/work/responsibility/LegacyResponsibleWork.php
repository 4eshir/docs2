<?php

namespace frontend\models\work\responsibility;

use app\models\work\order\OrderMainWork;
use common\models\scaffold\LegacyResponsible;
use common\models\scaffold\LocalResponsibility;

/**
 * @property OrderMainWork $orderWork
 */
class LegacyResponsibleWork extends LegacyResponsible
{
    public static function fill($peopleStampId, $responsibilityType, $branch, $auditoriumId, $quant, $startDate, $endDate, $orderId)
    {
        $entity = new static();
        $entity->responsibility_type = $responsibilityType;
        $entity->branch = $branch;
        $entity->auditorium_id = $auditoriumId;
        $entity->quant = $quant;
        $entity->people_stamp_id = $peopleStampId;
        $entity->order_id = $orderId;
        $entity->start_date = $startDate;
        $entity->end_date = $endDate;

        return $entity;
    }

    public function getOrderWork()
    {
        return $this->hasOne(OrderMainWork::class, ['id' => 'order_id']);
    }
}