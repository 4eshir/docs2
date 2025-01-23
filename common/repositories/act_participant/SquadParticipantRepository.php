<?php

namespace common\repositories\act_participant;

use app\models\work\team\SquadParticipantWork;
use common\models\scaffold\SquadParticipant;
use Yii;
use function PHPUnit\Framework\throwException;

class SquadParticipantRepository
{
    public function prepareCreate($actParticipantId, $participantId){
        $model = SquadParticipantWork::fill($actParticipantId, $participantId);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function prepareDelete($actParticipantId, $participantId){
        $model = SquadParticipantWork::find()
            ->andWhere(['act_participant_id' => $actParticipantId])
            ->andWhere(['participant_id' => $participantId])
            ->one();
        $command = Yii::$app->db->createCommand();
        $command->delete($model::tableName(), $model->getAttributes());
        return $command->getRawSql();
    }

    public function getCountByActAndParticipantId($actId, $participantId){
        return count(SquadParticipantWork::find()->andWhere(['act_participant_id' => $actId, 'participant_id' => $participantId])->all());
    }

    public function getAllByActId($actId){
        return SquadParticipantWork::find()->andWhere(['act_participant_id' => $actId])->all();
    }

    public function getAllByActIds(array $actIds){
        return SquadParticipantWork::find()->andWhere(['IN', 'act_participant_id', $actIds])->all();
    }

    public function getAllFromEvent($foreignEventId)
    {
        return SquadParticipantWork::find()->joinWith(['actParticipantWork actParticipantWork'])->where(['actParticipantWork.foreign_event_id' => $foreignEventId])->all();
    }
}