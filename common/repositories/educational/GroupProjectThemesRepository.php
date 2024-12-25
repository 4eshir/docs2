<?php

namespace common\repositories\educational;

use DomainException;
use frontend\models\work\educational\training_group\GroupProjectsThemesWork;
use Yii;

class GroupProjectThemesRepository
{
    public function get($id)
    {
        return GroupProjectsThemesWork::find()->where(['id' => $id])->one();
    }

    public function getProjectThemesFromGroup($groupId)
    {
        return GroupProjectsThemesWork::find()->where(['training_group_id' => $groupId])->all();
    }

    public function prepareCreate($groupId, $themeId, $confirm)
    {
        $model = GroupProjectsThemesWork::fill($groupId, $themeId, $confirm);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(GroupProjectsThemesWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function save(GroupProjectsThemesWork $theme)
    {
        if (!$theme->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и темы проекта. Проблемы: '.json_encode($theme->getErrors()));
        }
        return $theme->id;
    }
}