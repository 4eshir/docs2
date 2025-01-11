<?php

namespace frontend\services\educational;

use app\events\educational\order_training_group_participant\DeleteOrderTrainingGroupParticipantEvent;
use app\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use app\models\work\order\OrderTrainingWork;
use common\models\scaffold\TrainingGroupParticipant;
use frontend\events\educational\order_training_group_participant\CreateOrderTrainingGroupParticipantEvent;
use yii\helpers\ArrayHelper;

class OrderTrainingGroupParticipantService
{
    public function addOrderTrainingGroupParticipantEvent(OrderTrainingWork $model, $orderId, $trainingGroupParticipants, $status){
        foreach ($trainingGroupParticipants as $trainingGroupParticipant){
            if($trainingGroupParticipant != NULL){
                $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($orderId, $trainingGroupParticipant, $status), OrderTrainingGroupParticipantWork::class);
            }
        }
    }
    public function updateOrderTrainingGroupParticipant(OrderTrainingWork $model, $orderId, $trainingGroupParticipants, $status){
        $formParticipants = $trainingGroupParticipants;
        $existsParticipants = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
            ->where(['order_id' => $orderId])
            ->all(),
            'training_group_participant_id');
        if($formParticipants == NULL && $existsParticipants == NULL){
            $deleteParticipants = NULL;
            $createParticipants = NULL;
        }
        else if($formParticipants != NULL && $existsParticipants == NULL) {
            $deleteParticipants = NULL;
            $createParticipants = $formParticipants;
        }
        else if($formParticipants == NULL && $existsParticipants != NULL) {
            $deleteParticipants = $existsParticipants;
            $createParticipants = NULL;
        }
        else if($formParticipants != NULL && $formParticipants != NULL){
            $deleteParticipants = array_diff($existsParticipants, $formParticipants);
            $createParticipants = array_diff($formParticipants, $existsParticipants);
        }
        if ($deleteParticipants != NULL) {
            foreach ($deleteParticipants as $deleteParticipant) {
                $model->recordEvent(new DeleteOrderTrainingGroupParticipantEvent($orderId, $deleteParticipant, $status), OrderTrainingGroupParticipantWork::class);
            }
        }
        if ($createParticipants != NULL) {
            foreach ($createParticipants as $createParticipant) {
                $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($orderId, $createParticipant, $status), OrderTrainingGroupParticipantWork::class);
            }
        }
    }
}