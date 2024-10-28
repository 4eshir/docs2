<?php

namespace frontend\models\work\responsibility;

use common\models\scaffold\LocalResponsibility;
use frontend\models\work\general\PeopleStampWork;

class LocalResponsibilityWork extends LocalResponsibility
{
    public function getPeopleStampWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'people_stamp_id']);
    }

    // Проверка на то, прикреплена ли ответственность к человеку
    public function isAttach()
    {
        return $this->people_stamp_id !== null;
    }
}