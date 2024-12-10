<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TrainingGroupParticipant;
use JsonSerializable;

class TrainingGroupParticipantWork extends TrainingGroupParticipant implements JsonSerializable
{
    public static function fill($groupId, $participantId, $sendMethod)
    {
        $entity = new static();
        $entity->training_group_id = $groupId;
        $entity->participant_id = $participantId;
        $entity->send_method = $sendMethod;

        return $entity;
    }

    public function jsonSerialize()
    {
        return [
            'participant_id' => $this->participant_id,
            'training_group_id' => $this->training_group_id,
        ];
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize());
    }
}