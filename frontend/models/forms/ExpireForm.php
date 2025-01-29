<?php

namespace frontend\models\forms;



use app\models\work\order\ExpireWork;
use yii\base\Model;

class ExpireForm extends Model
{
    public $activeRegulationId;
    public $expireRegulationId;
    public $expireOrderId;
    public $docType;
    public $expireType;
    public function attachAttributes(ExpireWork $model, $activeRegulationId, $expireRegulationId, $expireOrderId, $docType, $expireType){
        $model->active_regulation_id = $activeRegulationId;
        $model->expire_regulation_id = $expireRegulationId;
        $model->expire_order_id = $expireOrderId;
        $model->document_type = $docType;
        $model->expire_type = $expireType;
    }
}