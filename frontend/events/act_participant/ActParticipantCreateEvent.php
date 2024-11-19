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
    public $allowRemote;
    public $nomination;
    public $type;
    public $teamNameId;

    private ActParticipantRepository $actParticipantRepository;
    public function __construct(
        $teacherId,
        $teacher2Id,
        $teamNameId,
        $foreignEventId,
        $branch,
        $focus,
        $type,
        $allowRemote,
        $nomination
    )
    {
        $this->teacherId = $teacherId;
        $this->teacher2Id = $teacher2Id;
        $this->teamNameId = $teamNameId;
        $this->branch = $branch;
        $this->focus = $focus;
        $this->type = $type;
        $this->nomination = $nomination;
        $this->foreignEventId = $foreignEventId;
        $this->allowRemote = $allowRemote;
        $this->actParticipantRepository = Yii::createObject(ActParticipantRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return [
            $this->actParticipantRepository->prepareCreate(
                $this->teacherId,
                $this->teacher2Id,
                $this->teamNameId,
                $this->foreignEventId,
                $this->branch,
                $this->focus,
                $this->type,
                $this->allowRemote,
                $this->nomination
            )
        ];
    }
}