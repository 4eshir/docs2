<?php

namespace frontend\models\work\general;

use common\models\scaffold\PeopleStamp;

class PeopleStampWork extends PeopleStamp
{
    public static function fill($peopleId, $surname, $genitiveSurname, $positionId, $companyId)
    {
        $entity = new static();
        $entity->people_id = $peopleId;
        $entity->surname = $surname;
        $entity->genitive_surname = $genitiveSurname;
        $entity->position_id = $positionId;
        $entity->company_id = $companyId;

        return $entity;
    }
}
