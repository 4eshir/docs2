<?php

namespace common\repositories\providers\training_group;

use frontend\models\work\educational\training_group\TrainingGroupWork;

class TrainingGroupMockProvider implements TrainingGroupProviderInterface
{
    private array $dataStore = [];
    private array $participantsData = [];
    private array $lessonsData = [];
    private array $expertsData = [];
    private array $themesData = [];

    public function __construct(array $data = [])
    {
        $this->dataStore = $data;
    }

    public function get($id)
    {
        return $this->dataStore[$id] ?? null;
    }

    public function getAll()
    {
        return $this->dataStore;
    }

    public function getParticipants($id)
    {
        return $this->participantsData[$id] ?? [];
    }

    public function getGroupsForCertificates()
    {
        $date = date("Y-m-d", strtotime('+3 days'));
        return array_filter($this->dataStore, function($item) use ($date) {
            return $item['archive'] === 0 && $item['finish_date'] <= $date;
        });
    }

    public function getLessons($id)
    {
        return $this->lessonsData[$id] ?? [];
    }

    public function getExperts($id)
    {
        return $this->expertsData[$id] ?? [];
    }

    public function getThemes($id)
    {
        return $this->themesData[$id] ?? [];
    }

    public function save(TrainingGroupWork $model)
    {
        $model->id = count($this->dataStore);
        $this->dataStore[] = $model;
        return $model->id;
    }

    public function delete(TrainingGroupWork $model)
    {
        unset($this->dataStore[$model->id]);
        return true;
    }
}