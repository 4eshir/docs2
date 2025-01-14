<?php

namespace common\repositories\educational;

use common\repositories\providers\group_project_themes\GroupProjectThemesProvider;
use common\repositories\providers\group_project_themes\GroupProjectThemesProviderInterface;
use frontend\models\work\educational\training_group\GroupProjectsThemesWork;
use Yii;

class GroupProjectThemesRepository
{
    private $provider;

    public function __construct(GroupProjectThemesProviderInterface $provider = null)
    {
        if (!$provider) {
            $provider = Yii::createObject(GroupProjectThemesProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getProjectThemesFromGroup($groupId)
    {
        return $this->provider->getProjectThemesFromGroup($groupId);
    }

    public function prepareCreate($groupId, $themeId, $confirm)
    {
        return $this->provider->prepareCreate($groupId, $themeId, $confirm);
    }

    public function prepareDelete($id)
    {
        return $this->provider->prepareDelete($id);
    }

    public function save(GroupProjectsThemesWork $theme)
    {
        return $this->provider->save($theme);
    }
}