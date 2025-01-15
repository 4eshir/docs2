<?php


namespace common\repositories\educational;

use DomainException;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;
use yii\helpers\ArrayHelper;

class VisitRepository
{
    public function get($id)
    {
        return VisitWork::find()->where(['id' => $id])->one();
    }

    public function getByTrainingGroup($groupId)
    {
        return VisitWork::find()
            ->joinWith(['trainingGroupParticipantWork trainingGroupParticipantWork'])
            ->where(['IN', 'trainingGroupParticipantWork.training_group_id', $groupId])
            ->all();
    }

    public function getByGroupAndParticipant($groupId, $participantId)
    {
        return VisitWork::find()
            ->joinWith(['trainingGroupParticipantWork trainingGroupParticipantWork'])
            ->where(['trainingGroupParticipantWork.training_group_id' => $groupId])
            ->andWhere(['trainingGroupParticipantWork.participant_id' => $participantId])
            ->one();
    }

    public function delete(VisitWork $visit)
    {
        return $visit->delete();
    }

    public function save(VisitWork $visit)
    {
        if (!$visit->save()) {
            throw new DomainException('Ошибка сохранения образовательной программы. Проблемы: '.json_encode($visit->getErrors()));
        }
        return $visit->id;
    }

    public function getParticipantsFromGroup($groupId)
    {
        $visits = $this->getByTrainingGroup($groupId);
        return (Yii::createObject(TrainingGroupParticipantRepository::class))->getByParticipantIds(ArrayHelper::getColumn($visits, 'participant_id'));
    }

    public function getLessonsFromGroup($groupId)
    {
        /** @var VisitWork $visit */
        $visit = VisitWork::find()
            ->joinWith(['trainingGroupParticipantWork trainingGroupParticipantWork'])
            ->where(['IN', 'trainingGroupParticipantWork.training_group_id', $groupId])
            ->one();
        $lessonIds = VisitLesson::getLessonIds(VisitLesson::fromString($visit->lessons));
        return (Yii::createObject(TrainingGroupLessonRepository::class))->getByIds($lessonIds);
    }

    public function prepareUpdateLessons($visitIds, $lessons)
    {
        $command = Yii::$app->db->createCommand();
        $command->update(
            'visit',
            ['lessons' => $lessons],
            ['IN', 'id ', $visitIds],
        );
        return $command->getRawSql();
    }
}