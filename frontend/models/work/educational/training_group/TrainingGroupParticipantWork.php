<?php

namespace frontend\models\work\educational\training_group;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\models\scaffold\TrainingGroupParticipant;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\general\PeopleStampWork;
/**
 * @property ForeignEventParticipantsWork $participantWork
 * @property TrainingGroupWork $trainingGroupWork
 * @property GroupProjectThemesWork $groupProjectThemesWork
 */

class TrainingGroupParticipantWork extends TrainingGroupParticipant
{
    private const INIT_STATUS = 0;
    public static function fill(
        $groupId,
        $participantId,
        $sendMethod,
        $id = null,
        $status = self::INIT_STATUS
    )
    {
        $entity = new static();
        $entity->id = $id;
        $entity->training_group_id = $groupId;
        $entity->participant_id = $participantId;
        $entity->send_method = $sendMethod;
        $entity->status = $status;

        return $entity;
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

    public function getTrainingGroupWork()
    {
        return $this->hasOne(TrainingGroupWork::class, ['id' => 'training_group_id']);
    }

    public function getFullFio()
    {
        $model = ForeignEventParticipantsWork::findOne($this->participant_id);
        return $model->getFullFio();
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function getActivity($orderId){
        if($this->id != NULL && $orderId != NULL) {
            if (
                OrderTrainingGroupParticipantWork::find()
                    ->andWhere(['training_group_participant_in_id' => $this->id])
                    ->andWhere(['order_id' => $orderId])
                    ->count() +
                OrderTrainingGroupParticipantWork::find()
                    ->andWhere(['training_group_participant_out_id' => $this->id])
                    ->andWhere(['order_id' => $orderId])
                    ->count() > 0
            ) {
                return 1;
            }
        }
        return 0;
    }

    public function getActualGroup($modelId)
    {
        if($modelId != NULL) {
            $orderParticipant = OrderTrainingGroupParticipantWork::find()
                ->andWhere(['training_group_participant_out_id' => $this->id])
                ->andWhere(['order_id' => $modelId])
                ->one();
            if($orderParticipant == NULL) {
                return $this->training_group_id;
            }
            $participant = TrainingGroupParticipantWork::find()->andWhere(['id' => $orderParticipant->training_group_participant_in_id])->one();
            return $participant->training_group_id;
        }
        else {
            return $this->training_group_id;
        }
    }
    public function getFullStatusInfo(){
        $linkIn = OrderTrainingGroupParticipantWork::find()
            ->andWhere(['training_group_participant_in_id' => $this->id])
            ->andWhere(['<>' ,'training_group_participant_out_id' , ''])
            ->one();
        $linkOut = OrderTrainingGroupParticipantWork::find()
            ->andWhere(['training_group_participant_out_id' => $this->id])
            ->andWhere(['<>' ,'training_group_participant_in_id' , ''])
            ->one();
        switch ($this->status) {
            case NomenclatureDictionary::ORDER_INIT:
                return 'Не зачислен';
            case NomenclatureDictionary::ORDER_ENROLL;
                if (is_null($linkIn)) {
                    return 'Состоит в группе' . ' ' . $this->trainingGroupWork->number ;
                }
                else  {
                    $participant = TrainingGroupParticipantWork::findOne($linkIn->training_group_participant_out_id);
                    return 'Состоит в группе ' . $this->trainingGroupWork->number. ' Переведён из группы ' . $participant->trainingGroupWork->number . ' в группу ' . $this->trainingGroupWork->number;
                }
            case NomenclatureDictionary::ORDER_DEDUCT:
                if (is_null($linkOut)) {
                    return 'Отчислен из группы ' . $this->trainingGroupWork->number;
                }
                else
                {
                    $participant = TrainingGroupParticipantWork::findOne($linkOut->training_group_participant_in_id);
                    return 'Не состоит в группе ' . $this->trainingGroupWork->number . ' Переведён из группы ' . $this->trainingGroupWork->number. ' в группу ' .  $participant->trainingGroupWork->number;
                }
            default:
                return 'Ошибка статуса';
        }
    }

    public function setParticipantId(int $participantId)
    {
        $this->participant_id = $participantId;
    }

    public function getGroupProjectThemesWork()
    {
        return $this->hasOne(GroupProjectThemesWork::class, ['id' => 'group_project_themes_id']);
    }
}