<?php

namespace app\services\act_participant;

use app\events\act_participant\ActParticipantCreateEvent;
use common\models\scaffold\ActParticipant;
use frontend\forms\OrderEventForm;

class ActParticipantService
{

    public function addActParticipantEvent(OrderEventForm $model, $participantId, $teacherId, $teacher2Id, $foreignEventId,$branch,$focus, $allowRemoteId, $nomination)
    {
        $model->recordEvent(new ActParticipantCreateEvent($participantId, $teacherId, $teacher2Id, $foreignEventId,$branch,$focus, $allowRemoteId, $nomination), ActParticipant::class);

    }

}