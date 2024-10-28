<?php

namespace common\repositories\responsibility;

use DomainException;
use frontend\models\work\responsibility\LocalResponsibilityWork;

class LocalResponsibilityRepository
{
    public function get($id)
    {
        return LocalResponsibilityWork::find()->where(['id' => $id])->one();
    }

    public function save(LocalResponsibilityWork $responsibility)
    {
        if (!$responsibility->save()) {
            throw new DomainException('Ошибка сохранения ответственности. Проблемы: '.json_encode($responsibility->getErrors()));
        }

        return $responsibility->id;
    }
}