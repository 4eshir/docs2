<?php

namespace frontend\models\work\educational\training_group;


use common\events\EventTrait;
use common\models\scaffold\OrderTrainingGroupParticipant;

class OrderTrainingGroupParticipantWork extends OrderTrainingGroupParticipant
{
    use EventTrait;
    public static function fill(
        $trainingGroupParticipantOutId,
        $trainingGroupParticipantInId,
        $orderId
    ){
        $entity = new static();
        $entity->training_group_participant_out_id = $trainingGroupParticipantOutId;
        $entity->training_group_participant_in_id = $trainingGroupParticipantInId;
        $entity->order_id = $orderId;
        return $entity;
    }
}