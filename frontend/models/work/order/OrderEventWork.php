<?php

namespace app\models\work\order;

use common\models\scaffold\OrderMain;

class OrderEventWork extends OrderMainWork
{
    public static function fill(
         $order_copy_id, $order_number, $order_postfix,
         $order_date, $order_name, $signed_id,
         $bring_id, $executor_id, $key_words, $creator_id,
         $last_edit_id, $target, $type, $state, $nomenclature_id,
         $study_type, $scanFile, $docFiles
    ){
        $entity = new static();
        $entity->order_copy_id = $order_copy_id;
        $entity->order_number = $order_number;
        $entity->order_postfix = $order_postfix;
        $entity->order_date = $order_date;
        $entity->order_name = $order_name;
        $entity->signed_id = $signed_id;
        $entity->bring_id = $bring_id;
        $entity->executor_id = $executor_id;
        $entity->key_words = $key_words;
        $entity->creator_id = $creator_id;
        $entity->last_edit_id = $last_edit_id;
        //$entity->target = $target;
        $entity->type = $type;
        $entity->state = $state;
        $entity->nomenclature_id = $nomenclature_id;
        $entity->study_type = $study_type;
        $entity->scanFile = $scanFile;
        $entity->docFiles = $docFiles;
        return $entity;
    }
}