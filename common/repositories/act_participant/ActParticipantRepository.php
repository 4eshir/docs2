<?php

namespace common\repositories\act_participant;

use app\models\work\event\ForeignEventWork;
use app\models\work\team\ActParticipantWork;
use app\models\work\team\SquadParticipantWork;
use common\models\scaffold\ActParticipant;
use common\repositories\event\ForeignEventRepository;
use common\repositories\order\OrderEventRepository;
use DomainException;
use Yii;

class ActParticipantRepository
{
    public SquadParticipantRepository $squadParticipantRepository;
    public OrderEventRepository $orderEventRepository;
    public ForeignEventRepository $foreignEventRepository;
    public function __construct(
        SquadParticipantRepository $squadParticipantRepository,
        OrderEventRepository $orderEventRepository,
        ForeignEventRepository $foreignEventRepository
    ){
        $this->squadParticipantRepository = $squadParticipantRepository;
        $this->orderEventRepository = $orderEventRepository;
        $this->foreignEventRepository = $foreignEventRepository;
    }
    public function getByForeignEventId($foreignEventId){
        return ActParticipantWork::find()->where(['foreign_event_id' => $foreignEventId])->all();
    }
    public function prepareCreate($modelAct, $teamNameId, $foreignEventId)
    {
        $modelAct->save();
        return $modelAct->id;
    }
    public function getOneByUniqueAttributes($teamNameId, $nomination, $foreignEventId)
    {
        return ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['nomination' => $nomination])
            ->one();
    }
    public function getAllByUniqueAttributes($teamNameId, $nomination, $foreignEventId)
    {
        return ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['nomination' => $nomination])
            ->all();
    }
    public function getByTypeAndForeignEventId($foreignEventId, $type)
    {
        return ActParticipantWork::find()->andWhere(['foreign_event_id' => $foreignEventId])->andWhere(['type' => $type])->all();
    }
    public function checkUniqueAct($foreignEventId, $teamNameId, $focus, $form, $nomination)
    {
        return count(ActParticipantWork::find()
            ->andWhere(['foreign_event_id' => $foreignEventId])
            ->andWhere(['team_name_id' => $teamNameId])
            ->andWhere(['focus' => $focus])
            ->andWhere(['form' => $form])
            ->andWhere(['nomination' => $nomination])
            ->all());
    }
    public function getById($id)
    {
        return ActParticipantWork::findOne($id);
    }
    public function save($model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
    public function delete(ActParticipantWork $model)
    {
        $squadParticipants = $this->squadParticipantRepository->getAllByActId($model->id);
        foreach ($squadParticipants as $squadParticipant) {
            if (!$squadParticipant->delete()) {
                throw new DomainException('Ошибка удаления. Проблемы: '.json_encode($model->getErrors()));
            }
        }
        if (!$model->delete()) {
            throw new DomainException('Ошибка удаления. Проблемы: '.json_encode($model->getErrors()));
        }
    }
}