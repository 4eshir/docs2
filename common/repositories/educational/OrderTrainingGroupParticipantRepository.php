<?php

namespace common\repositories\educational;

use app\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use app\models\work\order\OrderTrainingWork;
use common\models\scaffold\OrderTrainingGroupParticipant;
use Yii;

class OrderTrainingGroupParticipantRepository
{
    public function prepareCreate($orderId, $trainingGroupParticipantId, $status)
    {
        $model = OrderTrainingGroupParticipantWork::fill($orderId, $trainingGroupParticipantId, $status);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function prepareDelete($orderId, $trainingGroupParticipantId, $status)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete(OrderTrainingGroupParticipantWork::tableName(),
            ['training_group_participant_id' => $trainingGroupParticipantId, 'order_id' => $orderId]);
        return $command->getRawSql();
    }
}