<?php
namespace app\services;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\ExpireWork;
use frontend\events\expire\ExpireCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;

class OrderMainService {
    public function addExpireEvent($docs, $regulation, $model) {
        if($docs[0] != NULL && $regulation[0] != NULL){
            for($i = 0; $i < count($docs); $i++){
                $model->recordEvent(new ExpireCreateEvent($regulation[$i],
                    $regulation[$i],$docs[$i],1,1), ExpireWork::class);
            }
        }
    }
    public function addOrderPeopleEvent($respPeople, $model)
    {
        if ($respPeople[0] != NULL) {
            for ($i = 0; $i < count($respPeople); $i++) {
                $model->recordEvent(new OrderPeopleCreateEvent($respPeople[$i], $model->id), OrderPeopleWork::class);
            }
        }
    }
}