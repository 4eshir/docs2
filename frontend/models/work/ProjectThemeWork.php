<?php

namespace frontend\models\work;

use common\models\scaffold\ProjectTheme;

class ProjectThemeWork extends ProjectTheme
{
    public static function fill(string $name, int $projectType, string $description)
    {
        $entity = new static();
        $entity->name = $name;
        $entity->project_type = $projectType;
        $entity->description = $description;

        return $entity;
    }
}
