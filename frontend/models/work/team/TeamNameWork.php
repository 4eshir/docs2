<?php

namespace app\models\work\team;

use common\models\scaffold\TeamName;

class TeamNameWork extends TeamName
{
    public static function fill($name, $foreignEventId)
    {
        $entity = new static();
        $entity->name = $name;
        $entity->foreign_event_id = $foreignEventId;
        return $entity;
    }
}