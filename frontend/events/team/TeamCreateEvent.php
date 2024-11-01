<?php

namespace app\events\team;

use common\events\EventInterface;
use common\repositories\team\TeamRepository;
use Yii;

class TeamCreateEvent implements EventInterface
{
    private $actParticipant;
    private $foreignEventId;
    private $participantId;
    private $teamNameId;
    private TeamRepository $teamRepository;
    public function __construct(
        $actParticipant,
        $foreignEventId,
        $participantId,
        $teamNameId
    )
    {
        $this->actParticipant = $actParticipant;
        $this->foreignEventId = $foreignEventId;
        $this->participantId = $participantId;
        $this->teamNameId = $teamNameId;
        $this->teamRepository = Yii::createObject(TeamRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        $this->teamRepository->prepareTeamCreate(
            $this->actParticipant,
            $this->foreignEventId,
            $this->participantId,
            $this->teamNameId
        );
    }
}