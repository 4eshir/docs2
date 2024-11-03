<?php

namespace common\repositories\event;

use app\models\work\event\ForeignEventWork;
use DomainException;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class ForeignEventRepository
{
    public function get($id)
    {
        return ForeignEventWork::find()->where(['id' => $id])->one();
    }
    public function getByDocOrderId($id)
    {
        return ForeignEventWork::find()->where(['order_participant_id' => $id])->one();
    }
    public function delete($id)
    {
        return ForeignEventWork::deleteAll(['id' => $id]);
    }
    public function getAll()
    {
        return ForeignEventWork::find()->all();
    }
    public function save(ForeignEventWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}