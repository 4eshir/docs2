<?php

namespace common\repositories\order;

use app\models\work\order\OrderTrainingWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\data\ActiveDataProvider;

class OrderTrainingRepository
{
    public function get($id)
    {
        return OrderTrainingWork::findOne($id);
    }
    public function getOrderTrainingGroupData(){
        $groups = new ActiveDataProvider([
            'query' => TrainingGroupWork::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $groups;
    }
    public function getOrderTrainingGroupParticipantData(){
        $groupParticipant = new ActiveDataProvider([
            'query' => TrainingGroupParticipantWork::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $groupParticipant;
    }
    public function getEmptyOrderTrainingGroupList(){
        return  new ActiveDataProvider([
            'query' => TrainingGroupWork::find()->where('0=1'), // Пустой результат
        ]);
    }
}