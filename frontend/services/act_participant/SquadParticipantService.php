<?php

namespace app\services\act_participant;

use app\events\act_participant\SquadParticipantCreateEvent;
use app\models\work\team\ActParticipantWork;
use app\models\work\team\SquadParticipantWork;
use app\models\work\team\TeamNameWork;
use common\models\scaffold\SquadParticipant;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\team\TeamRepository;
use frontend\forms\OrderEventForm;

class SquadParticipantService
{
    public ActParticipantRepository $actParticipantRepository;
    public TeamRepository $teamRepository;
    public function __construct(
        ActParticipantRepository $actParticipantRepository,
        TeamRepository $teamRepository
    )
    {
        $this->actParticipantRepository = $actParticipantRepository;
        $this->teamRepository = $teamRepository;
    }
}