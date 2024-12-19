<?php

namespace common\repositories\educational;

use DomainException;
use frontend\models\work\ProjectThemeWork;

class ProjectThemeRepository
{
    public function get($id)
    {
        return ProjectThemeWork::find()->where(['id' => $id])->one();
    }

    public function save(ProjectThemeWork $theme)
    {
        if (!$theme->save()) {
            throw new DomainException('Ошибка сохранения темы проекта. Проблемы: '.json_encode($theme->getErrors()));
        }
        return $theme->id;
    }
}