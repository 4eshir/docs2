<?php

namespace common\repositories\providers\group_lesson;

use frontend\models\work\educational\training_group\TrainingGroupLessonWork;

class TrainingGroupLessonMockProvider implements TrainingGroupLessonProviderInterface
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get($id)
    {
        return $this->data[$id] ?? null;
    }

    public function getLessonsFromGroup($id)
    {
        return array_filter($this->data, function($item) use ($id) {
            return $item['training_group_id'] === $id;
        });
    }

    public function delete(TrainingGroupLessonWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(TrainingGroupLessonWork $model)
    {
        $this->data[] = $model;
        return count($this->data) - 1;
    }
}