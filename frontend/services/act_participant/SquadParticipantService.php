<?php

namespace app\services\act_participant;

use app\events\act_participant\SquadParticipantCreateEvent;
use app\models\work\team\ActParticipantWork;
use app\models\work\team\SquadParticipantWork;
use app\models\work\team\TeamNameWork;
use common\Model;
use common\models\scaffold\SquadParticipant;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\act_participant\SquadParticipantRepository;
use common\repositories\team\TeamRepository;
use frontend\forms\OrderEventForm;

class SquadParticipantService
{
    public ActParticipantRepository $actParticipantRepository;
    public TeamRepository $teamRepository;
    public SquadParticipantRepository $squadParticipantRepository;
    public function __construct(
        ActParticipantRepository $actParticipantRepository,
        TeamRepository $teamRepository,
        SquadParticipantRepository $squadParticipantRepository
    )
    {
        $this->actParticipantRepository = $actParticipantRepository;
        $this->teamRepository = $teamRepository;
        $this->squadParticipantRepository = $squadParticipantRepository;
    }
    public function addSquadParticipantEvent(ActParticipantWork $model, $participants, $actId){
        if($participants != NULL) {
            foreach ($participants as $participant) {
                if($this->squadParticipantRepository->getCountByActAndParticipantId($actId, $participant) == 0) {
                    $model->recordEvent(new SquadParticipantCreateEvent($actId, $participant), SquadParticipantWork::class);
                }
            }
            $model->releaseEvents();
        }
    }
}