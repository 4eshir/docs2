<?php

namespace common\repositories\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\repositories\providers\training_group\TrainingGroupProvider;
use common\repositories\providers\training_group\TrainingGroupProviderInterface;
use DomainException;
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

    public function getAll()
    {
        return $this->provider->getAll();
    }

    public function getByTeacher($teacherId)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByTeacher($teacherId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByTeacher');
        }
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

    public function getByBranchQuery($branch)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByBranchQuery($branch);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByBranchQuery');
        }
    }

    public function getByBranches(array $branches)
    {
        if (get_class($this->provider) == TrainingGroupProvider::class) {
            return $this->provider->getByBranch($branches);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByBranches');
        }
    }
    public function empty(){
        return TrainingGroupWork::find()->where(['id' => 0]);
    }
    public function getById($id)
    {
        return TrainingGroupWork::findAll(['id' => $id]);
    }
}