<?php

namespace frontend\models\work\educational\training_group;

use common\models\scaffold\GroupProjectThemes;
use common\models\scaffold\TrainingGroupExpert;
use common\models\scaffold\TrainingGroupLesson;
use common\repositories\dictionaries\AuditoriumRepository;
use frontend\models\work\dictionaries\AuditoriumWork;
use Yii;

class GroupProjectsThemesWork extends GroupProjectThemes
{

    public static function fill(int $groupId, int $themeId, int $confirm)
    {
        $entity = new static();
        $entity->training_group_id = $groupId;
        $entity->project_theme_id = $themeId;
        $entity->confirm = $confirm;

        return $entity;
    }
}