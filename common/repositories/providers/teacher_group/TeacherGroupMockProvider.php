<?php

namespace common\repositories\providers\teacher_group;

use frontend\models\work\educational\training_group\TeacherGroupWork;

class TeacherGroupMockProvider implements TeacherGroupProviderInterface
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getAllTeachersFromGroup($groupId)
    {
        return array_filter($this->data, function($item) use ($groupId) {
            return $item['training_group_id'] === $groupId;
        });
    }

    public function save(TeacherGroupWork $teacher)
    {
        $this->data[] = $teacher;
        return count($this->data) - 1;
    }
}