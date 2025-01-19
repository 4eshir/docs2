<?php

namespace common\repositories\order;
use app\models\work\order\OrderTrainingWork;
use app\services\order\OrderTrainingService;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OrderTrainingRepository
{
    public OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    public TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    public TrainingGroupRepository $trainingGroupRepository;
    public OrderTrainingService $orderTrainingService;
    public function __construct(
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        TrainingGroupRepository $trainingGroupRepository,
        OrderTrainingService $orderTrainingService
    )
    {
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->orderTrainingService = $orderTrainingService;
    }

    public function get($id)
    {
        return OrderTrainingWork::findOne($id);
    }
    public function getOrderTrainingGroupData(OrderTrainingWork $model){

        $groups = new ActiveDataProvider([
            'query' => TrainingGroupWork::find()->where(['branch' => $model->branch]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $groups;
    }
    public function getOrderTrainingGroupParticipantData(OrderTrainingWork $model){
        $orderId = $model->id;

        $participantId = ArrayHelper::getColumn($this->orderTrainingGroupParticipantRepository->getByOrderId($orderId),
            'training_group_participant_id');
        $groupId = ArrayHelper::getColumn($this->trainingGroupParticipantRepository->getAll($participantId),
            'training_group_id');
        $status = $this->orderTrainingService->getStatus($model);
        if ($status == 0) {
            $groupParticipant = new ActiveDataProvider([
                'query' => $this->trainingGroupParticipantRepository->getParticipantToDeductUpdate($groupId, $orderId),
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }
        if($status == 1) {
            $groupParticipant = new ActiveDataProvider([
                'query' => $this->trainingGroupParticipantRepository->getParticipantToEnrollUpdate($groupId, $orderId),
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }
        return $groupParticipant;
    }
    public function getEmptyOrderTrainingGroupParticipantData(){
        $groupParticipant = new ActiveDataProvider([
            'query' => TrainingGroupParticipantWork::find()->where('0=1'),
        ]);
        return $groupParticipant;
    }
    public function getEmptyOrderTrainingGroupList(){
        return new ActiveDataProvider([
            'query' => TrainingGroupWork::find()->where('0=1'), // Пустой результат
        ]);
    }
}