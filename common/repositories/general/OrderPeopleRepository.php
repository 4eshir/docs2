<?php

namespace common\repositories\general;

use app\models\work\general\OrderPeopleWork;
use Yii;

class OrderPeopleRepository
{
    public function prepareCreate($people_id, $order_id){
        $model =OrderPeopleWork::fill($people_id, $order_id);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
}