<?php

namespace common\repositories\log;

use common\models\work\LogWork;
use DomainException;

class LogRepository
{
    public function get($id)
    {
        return LogWork::find()->where(['id' => $id])->one();
    }

    public function save(LogWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}