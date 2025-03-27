<?php

namespace common\models\work;

use common\helpers\StringFormatter;
use common\models\scaffold\Errors;

class ErrorsWork extends Errors
{
    public static function fill(
        string $error,
        string $tableName,
        int $rowId,
        string $createDatetime = '',
        int $wasAmnesty = 0
    ): ErrorsWork
    {
        if (StringFormatter::isEmpty($createDatetime)) {
            $createDatetime = date('Y-m-d H:i:s');
        }

        $entity = new static();
        $entity->error = $error;
        $entity->table_name = $tableName;
        $entity->table_row_id = $rowId;
        $entity->create_datetime = $createDatetime;
        $entity->was_amnesty= $wasAmnesty;

        return $entity;
    }
}