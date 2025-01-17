<?php

namespace app\models\work\educational\training_group;

use common\models\scaffold\OrderTrainingGroupParticipant;

class OrderTrainingGroupParticipantWork extends OrderTrainingGroupParticipant
{
    public static function fill(
        $orderId,
        $trainingGroupParticipantId
    ){
        $entity = new static();
        $entity->order_id = $orderId;
        $entity->training_group_participant_id = $trainingGroupParticipantId;
        return $entity;
    }
}