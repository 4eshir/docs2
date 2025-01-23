<?php

namespace common\repositories\providers\people;

use frontend\models\work\general\PeopleWork;
use Yii;

class PeopleMockProvider implements PeopleProviderInterface
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

    public function save(PeopleWork $model)
    {
        $this->data[] = $model;
        return count($this->data) - 1;
    }

    public function delete(PeopleWork $model)
    {
        unset($this->data[$model->id]);
        return true;
    }
}