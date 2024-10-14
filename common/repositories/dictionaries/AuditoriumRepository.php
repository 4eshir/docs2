<?php

namespace common\repositories\dictionaries;

use DomainException;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use yii\helpers\ArrayHelper;

class AuditoriumRepository
{
    public function get($id)
    {
        return AuditoriumWork::find()->where(['id' => $id])->one();
    }

    public function save(AuditoriumWork $aud)
    {
        if (!$aud->save()) {
            throw new DomainException('Ошибка создания помещения. Проблемы: '.json_encode($aud->getErrors()));
        }

        return $aud->id;
    }
}