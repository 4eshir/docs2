<?php

namespace frontend\models\work\responsibility;

use common\events\EventTrait;
use common\models\scaffold\LocalResponsibility;
use frontend\models\work\dictionaries\AuditoriumWork;
use frontend\models\work\general\PeopleStampWork;

/**
 * @property PeopleStampWork $peopleStampWork
 * @property AuditoriumWork $auditoriumWork
 */

class LocalResponsibilityWork extends LocalResponsibility
{
    use EventTrait;

    public $filesList;

    public static function fill($responsibilityType, $branch, $auditoriumId, $quant, $peopleStampId, $regulationId, $filesList)
    {
        $entity = new static();
        $entity->responsibility_type = $responsibilityType;
        $entity->branch = $branch;
        $entity->auditorium_id = $auditoriumId;
        $entity->quant = $quant;
        $entity->people_stamp_id = $peopleStampId;
        $entity->regulation_id = $regulationId;
        $entity->filesList = $filesList;

        return $entity;
    }

    public function getPeopleStampWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'people_stamp_id']);
    }

    public function getAuditoriumWork()
    {
        return $this->hasOne(AuditoriumWork::class, ['id' => 'auditorium_id']);
    }
}