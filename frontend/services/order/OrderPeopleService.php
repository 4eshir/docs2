<?php

namespace frontend\services\order;

use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use common\models\scaffold\DocumentOrder;
use common\repositories\general\OrderPeopleRepository;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\events\general\OrderPeopleDeleteEvent;

class OrderPeopleService
{
    private OrderPeopleRepository $orderPeopleRepository;
    public function __construct(
        OrderPeopleRepository $orderPeopleRepository
    )
    {
        $this->orderPeopleRepository = $orderPeopleRepository;
    }

    public function addOrderPeopleEvent($respPeople, $model)
    {
        if (is_array($respPeople)) {
            $respPeople = array_unique($respPeople);
            foreach ($respPeople as $person) {
                if ($person != NULL) {
                    if ($this->orderPeopleRepository->checkUnique($person, $model->id)) {
                        $model->recordEvent(new OrderPeopleCreateEvent($person, $model->id), OrderPeopleWork::class);
                    }
                }
            }
        }
    }
    public function deleteOrderPeopleEvent($respPeople, $model){
        if (is_array($respPeople)) {
            $respPeople = array_unique($respPeople);
            foreach ($respPeople as $person) {
                if ($person != NULL) {
                    if (!$this->orderPeopleRepository->checkUnique($person, $model->id)) {
                        $model->recordEvent(new OrderPeopleDeleteEvent($person, $model->id), OrderPeopleWork::class);
                    }
                }
            }
        }
    }
    public function updateOrderPeopleEvent($respPeople, $formRespPeople ,  $model)
    {
        if($respPeople != NULL && $formRespPeople != NULL) {
            $addSquadParticipant = array_diff($formRespPeople, $respPeople);
            $deleteSquadParticipant = array_diff($respPeople, $formRespPeople);
        }
        else if($formRespPeople == NULL && $respPeople != NULL) {
            $deleteSquadParticipant = $respPeople;
            $addSquadParticipant = NULL;
        }
        else if($respPeople == NULL && $formRespPeople != NULL) {
            $addSquadParticipant = $formRespPeople;
            $deleteSquadParticipant = NULL;
        }
        else {
            $deleteSquadParticipant = NULL;
            $addSquadParticipant = NULL;
        }
        if($deleteSquadParticipant != NULL) {
            $this->deleteOrderPeopleEvent($deleteSquadParticipant, $model);
        }
        if($addSquadParticipant != NULL) {
            $this->addOrderPeopleEvent($addSquadParticipant, $model);
        }
    }
}