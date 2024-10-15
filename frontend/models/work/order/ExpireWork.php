<?php

namespace app\models\work\order;

use common\models\scaffold\Expire;
use common\repositories\order\OrderMainRepository;
use common\repositories\regulation\RegulationRepository;
use frontend\models\work\regulation\RegulationWork;

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
    public function getStatus() {
        return $this->expire_type == 1 ? 'Утратило силу' : 'Изменено';
    }
    public function getNumber()
    {
        $info = NULL;
        if($this->expire_order_id){
            /* @var OrderMainWork $order */
            $order = OrderMainWork::find()->where(['id' => $this->expire_order_id])->one();
            $info = $order->order_name;
        }
        if($this->expire_regulation_id){
            /* @var RegulationWork $regulation */
            $regulation = RegulationWork::find()->where(['id' => $this->expire_regulation_id])->one();
            $info = $regulation->name;
        }
        return $info;
    }
    public function getType(
    ) {
        return $this->expire_regulation_id == NULL ? 'Приказ' : 'Положение';
    }
}