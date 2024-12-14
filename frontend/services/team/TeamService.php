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
            if($name != NULL && $name != "NULL") {
                $model->recordEvent(new TeamNameCreateEvent($model, $name, $foreignEventId), TeamNameWork::class);
                $model->releaseEvents();
            }
        }
        else {
            $model = $this->teamRepository->getByNameAndForeignEventId($foreignEventId, $name);
        }
        return $model->id;
    }
    public function getNamesByForeignEventId($foreignEventId){
        $teams = $this->teamRepository->getNamesByForeignEventId($foreignEventId);
        return ArrayHelper::getColumn($teams, 'name');
    }
}