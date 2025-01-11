<?php

namespace app\events\educational\order_training_group_participant;

use common\events\EventInterface;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;

class DeleteOrderTrainingGroupParticipantEvent implements EventInterface
{
    public $id;
    public $orderId;
    public $trainingGroupParticipantId;
    public $status;
    public OrderTrainingGroupParticipantRepository $repository;
    public function __construct(
        $orderId,
        $trainingGroupParticipantId,
        $status
    ){
        $this->orderId = $orderId;
        $this->trainingGroupParticipantId = $trainingGroupParticipantId;
        $this->status = $status;
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
            $this->repository->prepareDelete(
                $this->orderId,
                $this->trainingGroupParticipantId,
                $this->status
            )
        ];
    }
}