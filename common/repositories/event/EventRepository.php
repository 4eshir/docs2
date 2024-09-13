<?php

namespace common\repositories\event;

use frontend\models\work\event\EventBranchWork;
use frontend\models\work\event\EventWork;

class EventRepository
{
    public function get($id)
    {
        return EventWork::find()->where(['id' => $id])->one();
    }

    public function getBranches($id)
    {
        return EventBranchWork::find()->where(['event_id' => $id])->orderBy(['branch' => SORT_ASC])->all();
    }

    public function getEventNumber($object)
    {
        if ($object->id !== null)
            return $object->id;
        $events = EventWork::find()->orderBy(['id' => SORT_DESC])->all();
        return $events[0]->id + 1;
    }
}