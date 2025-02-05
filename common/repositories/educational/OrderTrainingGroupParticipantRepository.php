<?php

namespace common\repositories\educational;



use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use Yii;

class OrderTrainingGroupParticipantRepository
{

    public function prepareCreate(
        $trainingGroupParticipantOutId,
        $trainingGroupParticipantInId,
        $orderId
    ){
        $model =  OrderTrainingGroupParticipantWork::fill(
            $trainingGroupParticipantOutId,
            $trainingGroupParticipantInId,
            $orderId
        );
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function prepareDelete($trainingGroupParticipantOutId, $trainingGroupParticipantInId, $orderId)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(OrderTrainingGroupParticipantWork::tableName(),
            [
                'training_group_participant_in_id' => $trainingGroupParticipantInId,
                'order_id' => $orderId,
                'training_group_participant_out_id' => $trainingGroupParticipantOutId
            ]);
        return $command->getRawSql();
    }
    public function getUnique($trainingGroupParticipantOutId, $orderId){
        return OrderTrainingGroupParticipantWork::find()
            //->andWhere(['training_group_participant_in_id' => $trainingGroupParticipantInId])
            ->andWhere(['training_group_participant_out_id' => $trainingGroupParticipantOutId])
            ->andWhere(['order_id' => $orderId])
            ->one();
    }
    public function countByTrainingGroupParticipantOutId($trainingGroupParticipantOutId){
        return OrderTrainingGroupParticipantWork::find()->where(['training_group_participant_out_id' => $trainingGroupParticipantOutId])->count();
    }
    public function getByOrderIds($id){
        return OrderTrainingGroupParticipantWork::find()->where(['order_id' => $id])->all();
    }

}