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

    public function getAll()
    {
        return $this->data;
    }

    public function getByIds($ids)
    {
        return array_filter($this->data, function($key) use ($ids) {
            return in_array($key, $ids);
        }, ARRAY_FILTER_USE_BOTH);
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
        $model->id = count($this->data);
        $this->data[] = $model;
        return $model->id;
    }
}