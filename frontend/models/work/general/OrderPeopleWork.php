<?php

namespace app\models\work\general;

use common\models\scaffold\OrderPeople;

class OrderPeopleWork extends OrderPeople
{
    public static function fill(
        $people_id,
        $order_id
    )
    {
        $entity = new static();
        $entity->people_id = $people_id;
        $entity->order_id = $order_id;
        return $entity;
    }
}