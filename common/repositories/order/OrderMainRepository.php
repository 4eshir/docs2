<?php

namespace common\repositories\order;

use app\models\work\order\OrderMainWork;
use DomainException;
use setasign\Fpdi\PdfParser\Filter\Ascii85;

class OrderMainRepository
{
    public function save(OrderMainWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения входящего документа. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}