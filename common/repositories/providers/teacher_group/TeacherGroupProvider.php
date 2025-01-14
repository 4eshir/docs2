<?php

namespace common\repositories\providers\teacher_group;

use DomainException;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use Yii;

class TeacherGroupProvider implements TeacherGroupProviderInterface
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

    public function save(TeacherGroupWork $teacher)
    {
        if (!$teacher->save()) {
            throw new DomainException('Ошибка сохранения связки учебной группы и педагога. Проблемы: '.json_encode($teacher->getErrors()));
        }
        return $teacher->id;
    }
}