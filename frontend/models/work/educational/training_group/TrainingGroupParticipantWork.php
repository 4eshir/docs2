<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TrainingGroupParticipant;

class TrainingGroupParticipantWork extends TrainingGroupParticipant
{
    public static function fill($groupId, $participantId, $sendMethod)
    {
        $entity = new static();
        $entity->training_group_id = $groupId;
        $entity->participant_id = $participantId;
        $entity->send_method = $sendMethod;

        return $entity;
    }

    public function __toString()
    {
        return "[ParticipantID: $this->participant_id][GroupID: $this->training_group_id]";
    }
}