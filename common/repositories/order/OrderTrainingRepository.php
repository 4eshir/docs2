<?php

namespace common\repositories\order;

use app\models\work\order\OrderTrainingWork;

class OrderTrainingRepository
{
    public function get($id)
    {
        return OrderTrainingWork::findOne($id);
    }
}