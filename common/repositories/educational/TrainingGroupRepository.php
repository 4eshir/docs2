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

    private $provider;

    public function __construct(
        TrainingGroupProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getParticipants($id)
    {
        return $this->provider->getParticipants($id);
    }

    public function getLessons($id)
    {
        return $this->provider->getLessons($id);
    }

    public function getExperts($id)
    {
        return $this->provider->getExperts($id);
    }

    public function getThemes($id)
    {
        return $this->provider->getThemes($id);
    }

    public function save(TrainingGroupWork $group)
    {
        return $this->provider->save($group);
    }

    public function delete(TrainingGroupWork $model)
    {
        return $this->provider->delete($model);
    }
    public function getByBranch($branch)
    {
        return TrainingGroupWork::find()->where(['branch' => $branch]);
    }
}