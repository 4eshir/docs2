<?php

namespace app\services\act_participant;

use app\events\act_participant\SquadParticipantCreateEvent;
use app\models\work\team\ActParticipantBranchWork;
use app\models\work\team\ActParticipantWork;
use common\models\scaffold\ActParticipantBranch;
use common\repositories\act_participant\ActParticipantBranchRepository;

class ActParticipantBranchService
{
    private ActParticipantBranchRepository $actParticipantBranchRepository;
    public function __construct(
        ActParticipantBranchRepository $actParticipantBranchRepository
    )
    {
        $this->actParticipantBranchRepository = $actParticipantBranchRepository;
    }
    public function addActParticipantBranchEvent($actId, $branch ){
        $this->actParticipantBranchRepository->prepareCreate($actId, $branch);
    }
}