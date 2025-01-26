<?php

namespace common\repositories\providers\training_group;

use frontend\models\work\educational\training_group\TrainingGroupWork;

interface TrainingGroupProviderInterface
{
    public function get($id);
    public function getParticipants($id);
    public function getLessons($id);
    public function getExperts($id);
    public function getThemes($id);
    public function save(TrainingGroupWork $model);
    public function delete(TrainingGroupWork $model);
}