<?php

namespace app\models\work\order;

use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\models\scaffold\OrderMain;
use InvalidArgumentException;

class OrderEventWork extends OrderMainWork
{
    use EventTrait;
    public $actFiles;
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
    public function fillUpdate(
        $order_copy_id, $order_number, $order_postfix,
        $order_date, $order_name, $signed_id,
        $bring_id, $executor_id, $key_words, $creator_id,
        $last_edit_id, $target, $type, $state, $nomenclature_id,
        $study_type, $scanFile, $docFiles)
    {
        $this->order_copy_id = $order_copy_id;
        $this->order_number = $order_number;
        $this->order_postfix = $order_postfix;
        $this->order_date = $order_date;
        $this->order_name = $order_name;
        $this->signed_id = $signed_id;
        $this->bring_id = $bring_id;
        $this->executor_id = $executor_id;
        $this->key_words = $key_words;
        $this->creator_id = $creator_id;
        $this->last_edit_id = $last_edit_id;
        //$this->target = $target;
        $this->type = $type;
        $this->state = $state;
        $this->nomenclature_id = $nomenclature_id;
        $this->study_type = $study_type;
        $this->scanFile = $scanFile;
        $this->docFiles = $docFiles;
    }
}