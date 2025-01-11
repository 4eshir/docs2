<?php

namespace common\repositories\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\repositories\providers\training_group\TrainingGroupProvider;
use common\repositories\providers\training_group\TrainingGroupProviderInterface;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;

class TrainingGroupRepository
{
    use CommonDatabaseFunctions;

    private $groupProvider;

    public function __construct(
        TrainingGroupProviderInterface $groupProvider = null
    )
    {
        if (!$groupProvider) {
            $groupProvider = Yii::createObject(TrainingGroupProvider::class);
        }

        $this->groupProvider = $groupProvider;
    }

    public function get($id)
    {
        return $this->groupProvider->get($id);
    }

    public function getParticipants($id)
    {
        return $this->groupProvider->getParticipants($id);
    }

    public function getLessons($id)
    {
        return $this->groupProvider->getLessons($id);
    }

    public function getExperts($id)
    {
        return $this->groupProvider->getExperts($id);
    }

    public function getThemes($id)
    {
        return $this->groupProvider->getThemes($id);
    }

    public function save(TrainingGroupWork $group)
    {
        return $this->groupProvider->save($group);
    }

    public function delete(TrainingGroupWork $model)
    {
        return $this->groupProvider->delete($model);
    }
}