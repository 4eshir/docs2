<?php

namespace app\events\act_participant;

use common\events\EventInterface;
use common\repositories\act_participant\SquadParticipantRepository;
use Yii;

class SquadParticipantDeleteEvent implements EventInterface
{
    public $actParticipantId;
    public $participantId;
    private SquadParticipantRepository $repository;
    public function __construct(
        $actParticipantId,
        $participantId
    )
    {
        $this->actParticipantId = $actParticipantId;
        $this->participantId = $participantId;
        $this->repository = Yii::createObject(SquadParticipantRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute()
    {
        return [
            $this->repository->prepareDelete(
                $this->actParticipantId,
                $this->participantId
            )
        ];
    }
}