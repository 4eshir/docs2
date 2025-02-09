<?php

namespace common\repositories\order;

use app\models\work\order\OrderEventGenerateWork;
use common\models\scaffold\OrderEventGenerate;
use DomainException;

class OrderEventGenerateRepository
{
    public function getByOrderId($orderId)
    {
        return OrderEventGenerateWork::findOne(['order_id' => $orderId]);
    }
    public function save(OrderEventGenerateWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}