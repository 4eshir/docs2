<?php

namespace common\models\work\general;

use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\models\scaffold\Files;
use common\models\work\document_in_out\DocumentInWork;

class FilesWork extends Files
{
    use EventTrait;

    public static function fill(
        $tableName,
        $tableRowId,
        $filetype,
        $filepath
    )
    {
        $entity = new static();
        $entity->table_name = $tableName;
        $entity->table_row_id = $tableRowId;
        $entity->file_type = $filetype;
        $entity->filepath = $filepath;

        return $entity;
    }
}