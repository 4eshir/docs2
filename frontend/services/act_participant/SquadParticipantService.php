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
    public function addSquadParticipantEvent(OrderEventForm $model, $teams, $persons, $foreignEventId){

        /* @var TeamNameWork $squad */
        /* @var ActParticipantWork $act */
        /* @var ActParticipantWork $acts */

        foreach ($teams as $team) {
            $teamName = $team['team'][0];
            $nomination = $team['nominations'][0][0];
            $participants = $team['participants'];
            $squad = $this->teamRepository->getByNameAndForeignEventId($foreignEventId, $teamName);
            $act = $this->actParticipantRepository->getOneByUniqueAttributes($squad->id, $nomination, $foreignEventId);
            foreach ($participants as $participantId) {
                $model->recordEvent(new SquadParticipantCreateEvent($act->id, $participantId[0]), SquadParticipantWork::class);
            }
        }
        foreach ($persons as $person) {
            $nomination = $person['nominations'][0];
            $participants = $person['participants'][0][0];
            $acts = $this->actParticipantRepository->getAllByUniqueAttributes(NULL, $nomination, $foreignEventId);
            foreach ($acts as $act) {
                $model->recordEvent(new SquadParticipantCreateEvent($act->id, $participants), SquadParticipantWork::class);
            }
        }
    }
}