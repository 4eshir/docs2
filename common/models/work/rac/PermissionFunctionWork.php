<?php

namespace common\models\work\rac;

use common\models\scaffold\PermissionFunction;

class PermissionFunctionWork extends PermissionFunction
{
    public static function fill($name, $shortCode)
    {
        $entity = new static();
        $entity->name = $name;

        return $entity;
    }
}
