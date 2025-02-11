<?php

namespace frontend\models\work\dictionaries;

use common\models\scaffold\PersonalDataParticipant;
use InvalidArgumentException;

class PersonalDataParticipantWork extends PersonalDataParticipant
{
    const STATUS_FREE = 0;
    const STATUS_RESTRICT = 1;

    public array $statuses = [
        self::STATUS_FREE,
        self::STATUS_RESTRICT
    ];

    public static function fill($participantId, $pdId, $status = self::STATUS_RESTRICT)
    {
        $entity = new static();
        $entity->participant_id = $participantId;
        $entity->personal_data = $pdId;
        $entity->status = $status;

        return $entity;
    }

    public function isRestrict()
    {
        return $this->status === self::STATUS_RESTRICT;
    }

    public function setStatus(int $status)
    {
        if (!in_array($status, $this->statuses)) {
            throw new InvalidArgumentException('Неизвестный статус разглашения ПД');
        }

        $this->status = $status;
    }
}
