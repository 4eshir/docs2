<?php

namespace common\repositories\educational;

use common\repositories\providers\group_participant\TrainingGroupParticipantProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;

class TrainingGroupParticipantRepository
{
    private $provider;

    public function __construct(
        TrainingGroupParticipantProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(TrainingGroupParticipantProvider::class);
        }

        $this->provider = $provider;
    }

    public function get($id)
    {
        return $this->provider->get($id);
    }

    /**
     * @param int[] $ids
     */
    public function getByParticipantIds(array $ids)
    {
        return TrainingGroupParticipantWork::find()->where(['IN', 'participant_id', $ids])->all();
    }

    public function getParticipantsFromGroup($groupId)
    {
        return $this->provider->getParticipantsFromGroup($groupId);
    }

    public function prepareCreate($groupId, $participantId, $sendMethod)
    {
        if (get_class($this->provider) == TrainingGroupParticipantProvider::class) {
            return $this->provider->prepareCreate($groupId, $participantId, $sendMethod);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareCreate');
        }
    }

    public function prepareDelete($id)
    {
        if (get_class($this->provider) == TrainingGroupParticipantProvider::class) {
            return $this->provider->prepareDelete($id);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareDelete');
        }
    }

    public function prepareUpdate($id, $participantId, $sendMethod)
    {
        if (get_class($this->provider) == TrainingGroupParticipantProvider::class) {
            return $this->provider->prepareUpdate($id, $participantId, $sendMethod);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода prepareUpdate');
        }
    }

    public function save(TrainingGroupParticipantWork $model)
    {
        return $this->provider->save($model);
    }

    public function delete(TrainingGroupParticipantWork $model)
    {
        return $this->provider->delete($model);
    }
}