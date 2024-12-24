<?php

namespace common\repositories\educational;

use DomainException;
use frontend\models\work\educational\training_group\GroupProjectsThemesWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use Yii;

class TrainingGroupExpertRepository
{
    public function get($id)
    {
        return TrainingGroupExpertWork::find()->where(['id' => $id])->one();
    }

    public function getExpertsFromGroup($groupId)
    {
        return TrainingGroupExpertWork::find()->where(['training_group_id' => $groupId])->all();
    }

    public function prepareCreate($groupId, $expertId, $expertType)
    {
        $model = TrainingGroupExpertWork::fill($groupId, $expertId, $expertType);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(TrainingGroupExpertWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function prepareUpdate($id, $expertId, $expertType)
    {
        $command = Yii::$app->db->createCommand();
        $command->update('training_group_expert', ['expert_id' => $expertId, 'expert_type' => $expertType], "id = $id");
        return $command->getRawSql();
    }

    public function save(TrainingGroupExpertWork $expert)
    {
        if (!$expert->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и эксперта. Проблемы: '.json_encode($expert->getErrors()));
        }
        return $expert->id;
    }
}