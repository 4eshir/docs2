<?php

namespace app\events\team;

use app\models\work\event\ForeignEventWork;
use common\events\EventInterface;
use common\repositories\team\TeamRepository;
use Yii;

class TeamNameCreateEvent implements EventInterface
{
    private $name;
    private $foreignEventId;
    private TeamRepository $teamRepository;
    public function __construct($name, $foreignEventId)
    {
        $this->name = $name;
        $this->foreignEventId = $foreignEventId;
        $this->teamRepository = Yii::createObject(TeamRepository::class);
    }
    public function isSingleton(): bool
    {
        return false;
    }
    public function execute() {
        return [
           $this->teamRepository->prepareTeamNameCreate(
               $this->name,
               $this->foreignEventId
           )
        ];
    }
}