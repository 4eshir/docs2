<?php

namespace common\repositories\expire;

use app\models\work\order\ExpireWork;
use common\models\scaffold\Expire;
use Yii;

class ExpireRepository
{
    public function prepareCreate(  $active_regulation_id, $expire_regulation_id, $expire_order_id,
                                    $document_type, $expire_type){
        $model = ExpireWork::fill($active_regulation_id,$expire_regulation_id,
                                    $expire_order_id,$document_type, $expire_type);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

}