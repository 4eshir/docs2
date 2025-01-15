<?php


namespace common\repositories\educational;

use common\repositories\providers\visit\VisitProvider;
use common\repositories\providers\visit\VisitProviderInterface;
use DomainException;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;
use yii\helpers\ArrayHelper;

class VisitRepository
{
    private $provider;

    public function __construct(
        VisitProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(VisitProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    public function getByTrainingGroup($groupId)
    {
        return $this->provider->getByTrainingGroup($groupId);
    }

    public function delete(VisitWork $visit)
    {
        return $this->provider->delete($visit);
    }

    public function save(VisitWork $visit)
    {
        return $this->provider->save($visit);
    }

    public function getParticipantsFromGroup($groupId)
    {
        if (get_class($this->provider) == VisitProvider::class) {
            return $this->provider->getParticipantsFromGroup($groupId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getParticipantsFromGroup');
        }
    }

    public function getLessonsFromGroup($groupId)
    {
        if (get_class($this->provider) == VisitProvider::class) {
            return $this->provider->getLessonsFromGroup($groupId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getLessonsFromGroup');
        }
    }
}