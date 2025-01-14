<?php

namespace common\repositories\providers\group_participant;

use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

class TrainingGroupParticipantMockProvider implements TrainingGroupParticipantProviderInterface
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

    public function getParticipantsFromGroup($groupId)
    {
        return array_filter($this->data, function($item) use ($groupId) {
            return $item['training_group_id'] === $groupId;
        });
    }

    public function delete(TrainingGroupParticipantWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }

    public function save(TrainingGroupParticipantWork $model)
    {
        $this->data[] = $model;
        return count($this->data) - 1;
    }
}