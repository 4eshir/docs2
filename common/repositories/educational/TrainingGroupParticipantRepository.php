<?php

namespace common\repositories\educational;

use common\repositories\providers\group_participant\TrainingGroupParticipantProvider;
use common\repositories\providers\group_participant\TrainingGroupParticipantProviderInterface;
use DomainException;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
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
        return $this->provider->getByParticipantIds($ids);
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
    public function getAll($id)
    {
        return TrainingGroupParticipantWork::find()->where(['id' => $id])->all();
    }
    public function empty()
    {
        return TrainingGroupParticipantWork::find()->where(['id' => 0]);
    }
    public function getParticipantsToEnrollCreate($groupIds)
    {
        return TrainingGroupParticipantWork::find()->andWhere(['training_group_id' => $groupIds])->andWhere(['status' => 0]);
    }
    public function getParticipantsToDeductCreate($groupIds)
    {
        return TrainingGroupParticipantWork::find()->andWhere(['training_group_id' => $groupIds])->andWhere(['status' => 1]);
    }
    public function getParticipantsToTransferCreate($groupIds)
    {
        return TrainingGroupParticipantWork::find()->andWhere(['training_group_id' => $groupIds])->andWhere(['status' => 1]);
    }
    public function getParticipantToEnrollUpdate($groupId, $orderId){
        $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['training_group_participant_out_id' => NULL])
            ->all(),
            'training_group_participant_in_id');
        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => 0]]);
        return $query;
    }
    public function getParticipantToDeductUpdate($groupId, $orderId){
        $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['training_group_participant_in_id' => NULL])
            ->all(),
            'training_group_participant_out_id');
        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => 1]]);
        return $query;
    }
    public function getParticipantToTransferUpdate($groupId, $orderId)
    {
        $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['IS NOT', 'training_group_participant_out_id', NULL])
            ->andWhere(['IS NOT', 'training_group_participant_in_id', NULL])
            ->all(),
            'training_group_participant_out_id');
        $exceptParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->andWhere(['order_id' => $orderId])->andWhere(['IS NOT', 'training_group_participant_out_id', NULL])
            ->andWhere(['IS NOT', 'training_group_participant_in_id', NULL])
            ->all(),
            'training_group_participant_in_id');

        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => 1]]);
        $query = $query->andWhere(['not in', 'id', $exceptParticipantId]);
        return $query;
    }
    public function setStatus($id, $status){
        $model = TrainingGroupParticipantWork::findOne($id);
        $model->setStatus($status);
        return $this->save($model);
    }
}