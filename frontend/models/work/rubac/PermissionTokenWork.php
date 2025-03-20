<?php

namespace frontend\models\work\rubac;

use common\models\scaffold\PermissionToken;

class PermissionTokenWork extends PermissionToken
{
    public static function fill(
        int $userId,
        int $permissionId,
        string $startTime,
        string $endTime,
        int $branch = null
    )
    {
        $entity = new static();
        $entity->user_id = $userId;
        $entity->function_id = $permissionId;
        $entity->branch = $branch;
        $entity->start_time = $startTime;
        $entity->end_time = $endTime;

        return $entity;
    }
}
