<?php

namespace common\repositories\order;

use app\models\work\order\OrderMainWork;
use DomainException;
use setasign\Fpdi\PdfParser\Filter\Ascii85;

class OrderMainRepository
{

    public function get($id)
    {
        return OrderMainWork::find()->where(['id' => $id])->one();
    }
    public function delete($id)
    {
        return OrderMainWork::deleteAll(['id' => $id]);
    }
    public function save(OrderMainWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения входящего документа. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}