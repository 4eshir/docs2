<?php

namespace app\services\team;

use app\events\team\TeamNameCreateEvent;
use app\models\work\event\ForeignEventWork;
use app\models\work\team\TeamNameWork;
use app\models\work\team\TeamWork;
use common\helpers\html\HtmlBuilder;
use common\repositories\team\TeamRepository;
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
    public function teamNameCreateEvent($foreignEventId, $name){

        if(!$this->teamRepository->getByNameAndForeignEventId($foreignEventId, $name)){
            $model = new TeamNameWork();
            $model->recordEvent(new TeamNameCreateEvent($model, $name, $foreignEventId), TeamNameWork::class);
            $model->releaseEvents();
        }
        else {
            $model = $this->teamRepository->getByNameAndForeignEventId($foreignEventId, $name);
        }
        return $model->id;
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