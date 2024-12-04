<?php

namespace common\repositories\educational;

use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupWork;

class TrainingGroupRepository
{
    public function save(TrainingGroupWork $group)
    {
        if (!$group->save()) {
            throw new DomainException('Ошибка сохранения учебной группы. Проблемы: '.json_encode($group->getErrors()));
        }
        return $group->id;
    }
}