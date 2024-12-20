<?php

namespace common\repositories\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\educational\training_group\DeleteTeachersFromGroupEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;

class TrainingGroupLessonRepository
{
    public function get($id)
    {
        return TrainingGroupLessonWork::find()->where(['id' => $id])->one();
    }

    public function getLessonsFromGroup($id)
    {
        return TrainingGroupLessonWork::find()->where(['training_group_id' => $id])->all();
    }

    public function prepareCreate($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration)
    {
        $model = TrainingGroupLessonWork::fill($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($id)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(TrainingGroupLessonWork::tableName(), ['id' => $id]);
        return $command->getRawSql();
    }

    public function delete(TrainingGroupLessonWork $model)
    {
        return $model->delete();
    }
}