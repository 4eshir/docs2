<?php

namespace app\events\act_participant;

use common\events\EventInterface;
use common\repositories\act_participant\ActParticipantRepository;
use Yii;

class ActParticipantCreateEvent implements EventInterface
{
    public $participantId;
    public $teacherId;
    public $teacher2Id;
    public $foreignEventId;
    public $branch;
    public $focus;
    public $allowRemoteId;
    public $nomination;
    private ActParticipantRepository $actParticipantRepository;
    public function __construct(
         $participantId,
         $teacherId,
         $teacher2Id,
         $foreignEventId,
         $branch,
         $focus,
         $allowRemoteId,
         $nomination
    )
    {
        $this->participantId = $participantId;
        $this->teacherId = $teacherId;
        $this->teacher2Id = $teacher2Id;
        $this->foreignEventId = $foreignEventId;
        $this->branch = $branch;
        $this->focus = $focus;
        $this->allowRemoteId = $allowRemoteId;
        $this->nomination = $nomination;
        $this->actParticipantRepository = Yii::createObject(ActParticipantRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return [
            $this->actParticipantRepository->prepareCreate(
                $this->participantId,
                $this->teacherId,
                $this->teacher2Id,
                $this->foreignEventId,
                $this->branch,
                $this->focus,
                $this->allowRemoteId,
                $this->nomination
            )
        ];
    }
}