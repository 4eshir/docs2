<?php

namespace frontend\events\educational\training_group;

use common\events\EventInterface;
use common\repositories\educational\GroupProjectThemesRepository;
use Yii;

class UpdateGroupThemeEvent implements EventInterface
{
    private $id;
    private $groupId;
    private $themeId;
    private $confirm;

    private GroupProjectThemesRepository $repository;

    public function __construct(
        $id,
        $groupId,
        $themeId,
        $confirm
    )
    {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->themeId = $themeId;
        $this->confirm = $confirm;
        $this->repository = Yii::createObject(GroupProjectThemesRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareUpdate(
                $this->id,
                $this->groupId,
                $this->themeId,
                $this->confirm
            )
        ];
    }
}