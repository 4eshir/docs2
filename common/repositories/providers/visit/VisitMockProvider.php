<?php

namespace common\repositories\providers\visit;

use frontend\models\work\educational\journal\VisitWork;

class VisitMockProvider implements VisitProviderInterface
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

    public function getByTrainingGroup($groupId)
    {
        return array_filter($this->data, function($item) use ($groupId) {
            return $item['training_group_id'] === $groupId;
        });
    }

    public function delete(VisitWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(VisitWork $model)
    {
        $this->data[] = $model;
        return count($this->data) - 1;
    }
}