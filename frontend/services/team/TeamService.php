<?php

namespace app\services\team;

use app\events\team\TeamCreateEvent;
use app\events\team\TeamNameCreateEvent;
use app\models\work\event\ForeignEventWork;
use app\models\work\team\TeamNameWork;
use app\models\work\team\TeamWork;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\Team;
use common\repositories\team\TeamRepository;
use frontend\forms\OrderEventForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class TeamService
{
    private TeamRepository $teamRepository;
    public function __construct(
        TeamRepository $teamRepository
    )
    {
       $this->teamRepository = $teamRepository;
    }

    public function addTeamNameEvent($teams, OrderEventForm $model, $foreignEventId)
    {
        if($teams != NULL) {
            foreach ($teams as $team) {
                if ($team != NULL && $foreignEventId != NULL) {
                    $model->recordEvent(new TeamNameCreateEvent($team['team'], $foreignEventId), TeamNameWork::class);
                }
            }
        }
    }
    public function addTeamEvent(OrderEventForm $model, $actParticipantid, $foreignEventId, $participantId, $teamNameId)
    {
        $model->recordEvent(new TeamCreateEvent($actParticipantid, $foreignEventId, $participantId, $teamNameId), TeamWork::class);
    }
    public function getTeamTable(ForeignEventWork $foreignEvent)
    {
        /* @var TeamWork $teams */
        $teams = $this->teamRepository->getByForeignEventId($foreignEvent->id);
        $table = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название команды'], ArrayHelper::getColumn($teams, 'name')),
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-team'),
                    [
                        'modelId' => array_fill(0, count($teams), $teams->id),
                        'fileId' => ArrayHelper::getColumn($teams, 'id')])
            ]
        );
        return $table;
    }
}