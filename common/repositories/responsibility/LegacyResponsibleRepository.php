<?php

namespace common\repositories\responsibility;

use frontend\models\work\responsibility\LegacyResponsibleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;

class LegacyResponsibleRepository
{
    public function get($id)
    {
        return LegacyResponsibleWork::find()->where(['id' => $id])->one();
    }

    public function getByResponsible(LocalResponsibilityWork $responsible)
    {
        return LegacyResponsibleWork::find()
            ->where(['responsibility_type' => $responsible->responsibility_type])
            ->andWhere(['branch' => $responsible->branch])
            ->andWhere(['auditorium_id' => $responsible->auditorium_id])
            ->andWhere(['quant' => $responsible->quant])
            ->all();
    }
}