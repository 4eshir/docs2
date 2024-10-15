<?php

namespace common\repositories\expire;

use app\models\work\order\ExpireWork;
use app\models\work\order\OrderMainWork;
use common\models\scaffold\Expire;
use common\repositories\order\OrderMainRepository;
use common\repositories\regulation\RegulationRepository;
use frontend\models\work\regulation\RegulationWork;
use Yii;

class ExpireRepository
{
    public OrderMainRepository $orderMainRepository;
    public RegulationRepository $regulationRepository;
    public function __construct(
        OrderMainRepository $orderMainRepository,
        RegulationRepository $regulationRepository
    )
    {
        $this->orderMainRepository = $orderMainRepository;
        $this->regulationRepository = $regulationRepository;
    }
    public function prepareCreate($active_regulation_id, $expire_regulation_id, $expire_order_id,
                                    $document_type, $expire_type){
        $model = ExpireWork::fill($active_regulation_id,$expire_regulation_id,
                                    $expire_order_id,$document_type, $expire_type);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }
    public function getExpireByActiveRegulationId($id){
        return ExpireWork::find()->where(['active_regulation_id'=>$id])->all();
        /* @var ExpireWork $model */
        $model = ExpireWork::find()->where(['active_regulation_id'=>$id])->all();
        $info = NULL;
        if($model->expire_order_id){
            /* @var OrderMainWork $order */
            $order = $this->orderMainRepository->get($model->expire_order_id);
            $info = $order->order_name;
        }
        if($model->expire_regulation_id){
            /* @var RegulationWork $regulation */
           $regulation = $this->regulationRepository->get($model->expire_regulation_id);
           $info = $regulation->id;
        }
        return $info;
    }
    public function get($id){
        return ExpireWork::find()->where(['id'=>$id])->one();
    }
    public function deleteByActiveRegulationId($id){
        /* @var ExpireWork $model */
        $model = $this->get($id);
        $model->delete();
    }
    public function checkUnique($modelId, $reg, $order, $type, $status){
        $model = ExpireWork::find()->andWhere([
            'active_regulation_id' => $modelId,
            'expire_regulation_id' => $reg,
            'expire_order_id' => $order,
            'document_type' => $type,
            'expire_type' => $status
        ])->one();
        return $model ? false : true;
    }
}