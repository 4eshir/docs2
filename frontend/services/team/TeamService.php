<?php

namespace app\services\team;

use app\events\team\TeamCreateEvent;
use app\events\team\TeamNameCreateEvent;
use app\models\work\team\TeamNameWork;
use app\models\work\team\TeamWork;
use frontend\forms\OrderEventForm;

class TeamService
{
    public function addTeamNameEvent($teams, OrderEventForm $model  , $foreignEventId)
    {
        foreach ($teams as $team) {
            if($team != NULL && $foreignEventId != NULL){
                $model->recordEvent(new TeamNameCreateEvent($team, $foreignEventId), TeamNameWork::class);
            }
        }
    }
    public function addTeamEvent(OrderEventForm $model, $actParticipantid, $foreignEventId, $participantId, $teamNameId)
    {
        $model->recordEvent(new TeamCreateEvent($actParticipantid, $foreignEventId, $participantId, $teamNameId), TeamWork::class);
    }
}