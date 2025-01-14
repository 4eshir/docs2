<?php

namespace common\repositories\providers\visit;

use DomainException;
use frontend\models\work\educational\journal\VisitWork;

class VisitProvider implements VisitProviderInterface
{
    public function get($id)
    {
        return VisitWork::find()->where(['id' => $id])->one();
    }

    public function getByTrainingGroup($groupId)
    {
        return VisitWork::find()->where(['training_group_id' => $groupId])->all();
    }

    public function delete(VisitWork $visit)
    {
        return $visit->delete();
    }

    public function save(VisitWork $visit)
    {
        if (!$visit->save()) {
            throw new DomainException('Ошибка сохранения явок. Проблемы: '.json_encode($visit->getErrors()));
        }
        return $visit->id;
    }
}