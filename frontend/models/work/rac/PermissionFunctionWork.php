<?php

namespace frontend\models\work\rac;

use common\models\scaffold\PermissionFunction;

class PermissionFunctionWork extends PermissionFunction
{
    public static function fill($name, $shortCode, $id = null)
    {
        $entity = new static();
        if ($id) {
            $entity->id = $id;
        }
        $entity->name = $name;
        $entity->short_code = $shortCode;

        return $entity;
    }
}
