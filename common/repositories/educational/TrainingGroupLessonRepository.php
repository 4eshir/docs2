<?php

namespace common\repositories\educational;

use common\repositories\providers\group_lesson\TrainingGroupLessonProvider;
use common\repositories\providers\group_lesson\TrainingGroupLessonProviderInterface;
use DomainException;
use frontend\events\visit\AddLessonToVisitEvent;
use frontend\events\visit\DeleteLessonFromVisitEvent;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use Yii;

class TrainingGroupLessonRepository
{
    private $provider;

    public function __construct(
        TrainingGroupLessonProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupLessonProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getByIds($ids)
    {
        return TrainingGroupLessonWork::find()->where(['IN', 'id', $ids])->all();
    }

    public function getLessonsFromGroup($id)
    {
        return $this->provider->getLessonsFromGroup($id);
    }

    public function prepareCreate($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration)
    {
        if (get_class($this->provider) == TrainingGroupLessonProvider::class) {
            return $this->provider->prepareCreate($groupId, $lessonDate, $lessonStartTime, $branch, $auditoriumId, $lessonEndTime, $duration);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareCreate');
        }
    }

    public function prepareDelete($id)
    {
        if (get_class($this->provider) == TrainingGroupLessonProvider::class) {
            return $this->provider->prepareDelete($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareDelete');
        }
    }

    public function delete(TrainingGroupLessonWork $lesson)
    {
        $lesson->recordEvent(new DeleteLessonFromVisitEvent($lesson->training_group_id, [$lesson]), get_class($lesson));
        $lesson->releaseEvents();
        return $lesson->delete();
    }

    public function save(TrainingGroupLessonWork $lesson)
    {
        $lesson->recordEvent(new AddLessonToVisitEvent($lesson->training_group_id, [$lesson]), get_class($lesson));

        if (!$lesson->save()) {
            throw new DomainException('Ошибка сохранения образовательной программы. Проблемы: '.json_encode($lesson->getErrors()));
        }
        $lesson->releaseEvents();
        return $lesson->id;
    }
}