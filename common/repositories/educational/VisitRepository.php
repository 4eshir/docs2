<?php


namespace common\repositories\educational;

use DomainException;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

class VisitRepository
{
    public function get($id)
    {
        return VisitWork::find()->where(['id' => $id])->one();
    }

    public function getByTrainingGroup($groupId)
    {
        return VisitWork::find()->where(['training_group_id' => $groupId])->all();
    }

    /**
     * @param $groupId
     * @param TrainingGroupLessonWork[] $lessons
     * @param TrainingGroupParticipantWork[] $participants
     */
    public function createJournal($groupId, array $lessons, array $participants)
    {
        // Удаляем существующий журнал
        $visits = $this->getByTrainingGroup($groupId);
        foreach ($visits as $visit) {
            $this->delete($visit);
        }

        // Создаем новый журнал
        foreach ($participants as $participant) {
            $visit = VisitWork::fill(
                $groupId,
                $participant->id,
                TrainingGroupLessonWork::convertLessonsToJson($lessons) ? : ''
            );
            $this->save($visit);
        }
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
}