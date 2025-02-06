<?php

namespace common\repositories\order;

use frontend\models\work\order\DocumentOrderWork;

class DocumentOrderRepository
{
    public function getAll()
    {
        return DocumentOrderWork::find()->all();
    }
}