<?php

namespace common\repositories\educational;

use frontend\models\work\educational\training_group\TeacherGroupWork;
use Yii;

class TeacherGroupRepository
{
    public function getAllTeachersFromGroup($groupId)
    {
        return TeacherGroupWork::find()->where(['training_group_id' => $groupId])->all();
    }

    public function prepareCreate($teacherId, $groupId)
    {
        $model = TeacherGroupWork::fill($teacherId, $groupId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(TeacherGroupWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }
}