<?php

namespace common\repositories\providers\visit;

use frontend\models\work\educational\journal\VisitWork;

interface VisitProviderInterface
{
    public function get($id);
    public function getByTrainingGroup($groupId);
    public function delete(VisitWork $model);
    public function save(VisitWork $model);
}