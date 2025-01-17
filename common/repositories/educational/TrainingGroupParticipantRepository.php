<?php

namespace common\repositories\educational;

use app\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use common\models\scaffold\OrderTrainingGroupParticipant;
use common\repositories\providers\group_participant\TrainingGroupParticipantProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Mpdf\Tag\Tr;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
    public function getAllByGroupQuery($groupId)
    {
        return TrainingGroupParticipantWork::find()->andWhere(['<>', 'status', 1])->andWhere(['training_group_id' => $groupId]);
    }
    public function getParticipantToEnrolUpdate($groupId, $orderId){
        $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()->where(['order_id' => $orderId])->all(),
            'training_group_participant_id');
        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => 0]]);
        return $query;
    }
    public function getAll($id)
    {
        return TrainingGroupParticipantWork::find()->where(['id' => $id])->all();
    }
}