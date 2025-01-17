<?php

namespace frontend\events\educational\order_training_group_participant;

use common\events\EventInterface;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;

class CreateOrderTrainingGroupParticipantEvent implements EventInterface
{
    public $id;
    public $orderId;
    public $trainingGroupParticipantId;
    public OrderTrainingGroupParticipantRepository $repository;
    public function __construct(
        $orderId,
        $trainingGroupParticipantId
    ){
        $this->orderId = $orderId;
        $this->trainingGroupParticipantId = $trainingGroupParticipantId;
        $this->repository = new OrderTrainingGroupParticipantRepository();
    }
    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        // TODO: Implement execute() method.
        return [
            $this->repository->prepareCreate(
                $this->orderId,
                $this->trainingGroupParticipantId,
            )
        ];
    }

}