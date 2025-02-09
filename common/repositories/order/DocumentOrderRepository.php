<?php

namespace common\repositories\order;

use frontend\models\work\order\DocumentOrderWork;

class DocumentOrderRepository
{
    public function getAll()
    {
        return DocumentOrderWork::find()->all();
    }
    public function getAllByType($type)
    {
        return DocumentOrderWork::find()->where(['type' => $type])->all();
    }
    public function getExceptByIdAndStatus($id, $type){
        return DocumentOrderWork::find()->andWhere(['<>', 'id', $id])->andWhere(['type' => $type])->all();
    }
}