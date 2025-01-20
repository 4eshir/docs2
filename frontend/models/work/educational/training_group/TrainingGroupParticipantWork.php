<?php

namespace frontend\models\work\educational\training_group;

use app\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use common\models\scaffold\TrainingGroupParticipant;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\general\PeopleStampWork;
/**
 * @property ForeignEventParticipantsWork participantWork
 */

class TrainingGroupParticipantWork extends TrainingGroupParticipant
{
    public static function fill($groupId, $participantId, $sendMethod, $id = null)
    {
        $entity = new static();
        $entity->id = $id;
        $entity->training_group_id = $groupId;
        $entity->participant_id = $participantId;
        $entity->send_method = $sendMethod;

        return $entity;
    }
    public function getOrderTrainingGroupParticipantRelation($orderId)
    {
        if ($orderId != null) {
            if(OrderTrainingGroupParticipantWork::find()
                ->andWhere(['training_group_participant_id' => $this->id])
                ->andWhere(['order_id' => $orderId])
                ->count()){
                return 1;
            }
        }
        return 0;
    }
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['id', 'integer']
        ]);
    }

    public function __toString()
    {
        return "[ParticipantID: $this->participant_id][GroupID: $this->training_group_id][SendMethod: $this->send_method]";
    }

    public function getParticipantWork()
    {
        return $this->hasOne(ForeignEventParticipantsWork::class, ['id' => 'participant_id']);
    }
    public function getFullFio()
    {
        $model = ForeignEventParticipantsWork::findOne($this->participant_id);
        return $model->getFullFio();
    }
    public function setStatus($status){
        $this->status = $status;
    }
}