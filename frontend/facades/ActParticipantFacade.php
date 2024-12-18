<?php

namespace frontend\facades;

use app\services\act_participant\ActParticipantService;
use app\services\team\TeamService;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\team\TeamRepository;
use yii\helpers\ArrayHelper;

class ActParticipantFacade
{
    private ActParticipantService $actParticipantService;
    private ActParticipantRepository $actParticipantRepository;
    private TeamService $teamService;
    private TeamRepository $teamRepository;
    private PeopleRepository $peopleRepository;
    public function __construct(
        ActParticipantService $actParticipantService,
        ActParticipantRepository $actParticipantRepository,
        TeamService $teamService,
        TeamRepository $teamRepository,
        PeopleRepository $peopleRepository
    )
    {
        $this->actParticipantService = $actParticipantService;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->teamService = $teamService;
        $this->teamRepository = $teamRepository;
        $this->peopleRepository = $peopleRepository;
    }

    public function prepareActFacade($act){
        $modelAct = $this->actParticipantService->createForms($act);
        $people = $this->peopleRepository->getOrderedList();
        $nominations = array_unique(ArrayHelper::getColumn($this->actParticipantRepository->getByForeignEventId($act[0]->foreign_event_id), 'nomination'));
        $teams = $this->teamService->getNamesByForeignEventId($act[0]->foreign_event_id);
        $defaultTeam = $this->teamRepository->getById($act[0]->team_name_id);
        $tables = $this->actParticipantService->createActFileTable($act[0]);
        return [
            'modelAct' => $modelAct,
            'people' => $people,
            'nominations' => $nominations,
            'teams' => $teams,
            'defaultTeam' => $defaultTeam,
            'tables' => $tables,
        ];
    }
}