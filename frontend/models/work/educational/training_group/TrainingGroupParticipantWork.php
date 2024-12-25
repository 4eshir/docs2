<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\TrainingGroupParticipant;
use frontend\models\work\general\PeopleStampWork;

/**
 * @property PeopleStampWork participantWork
 */

class TrainingGroupParticipantWork extends TrainingGroupParticipant
{
    public static function fill($groupId, $participantId, $sendMethod, $id = null)
    {
        $entity = new static();
        $entity->id = $id;
        $entity->training_group_id = $groupId;
        $entity->participant_id = $participantId;
        $entity->send_method = $sendMethod;

        return $entity;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer']
        ]);
    }

    public function __toString()
    {
        return "[ParticipantID: $this->participant_id][GroupID: $this->training_group_id]";
    }

    public function getParticipantWork()
    {
        return $this->hasOne(PeopleStampWork::class, ['id' => 'participant_id']);
    }
}