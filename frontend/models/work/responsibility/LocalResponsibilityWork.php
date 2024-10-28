<?php

namespace frontend\models\work\responsibility;

use common\models\scaffold\LocalResponsibility;
use frontend\models\work\general\PeopleStampWork;

class LocalResponsibilityWork extends LocalResponsibility
{
    public static function fill($responsibilityType, $branch, $auditoriumId, $quant, $peopleStampId, $regulationId)
    {
        $entity = new static();
        $entity->responsibility_type = $responsibilityType;
        $entity->branch = $branch;
        $entity->auditorium_id = $auditoriumId;
        $entity->quant = $quant;
        $entity->people_stamp_id = $peopleStampId;
        $entity->regulation_id = $regulationId;

        return $entity;
    }

    public function getPeopleStampWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'people_stamp_id']);
    }
}