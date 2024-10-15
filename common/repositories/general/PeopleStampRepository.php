<?php

namespace common\repositories\general;

use common\components\traits\CommonDatabaseFunctions;
use DomainException;
use frontend\models\work\general\PeopleStampWork;

class PeopleStampRepository
{
    use CommonDatabaseFunctions;

    public function save(PeopleStampWork $stamp)
    {
        if (!$stamp->save()) {
            throw new DomainException('Ошибка сохранения человека. Проблемы: '.json_encode($stamp->getErrors()));
        }

        return $stamp->id;
    }
}