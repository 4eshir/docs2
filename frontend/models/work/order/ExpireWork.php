<?php

namespace app\models\work\order;

use common\models\scaffold\Expire;

class ExpireWork extends Expire
{
    public static function fill(
        $active_regulation_id,
        $expire_regulation_id,
        $expire_order_id,
        $document_type,
        $expire_type
    )
    {
        $entity = new static();
        $entity->active_regulation_id = $active_regulation_id;
        $entity->expire_regulation_id = $expire_regulation_id;
        $entity->expire_order_id = $expire_order_id;
        $entity->document_type = $document_type;
        $entity->expire_type = $expire_type;
        return $entity;
    }
}