<?php

namespace backend\builders;

use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\db\ActiveQuery;

class GroupParticipantReportBuilder
{
    public function query() : ActiveQuery
    {
        return TrainingGroupParticipantWork::find();
    }

    public function joinWith(ActiveQuery $query, string $relation) : ActiveQuery
    {
        return $query->joinWith([$relation]);
    }

    public function filterBySex(ActiveQuery $query, array $sex = [PersonInterface::SEX_MALE, PersonInterface::SEX_FEMALE]) : ActiveQuery
    {
        return $query->andWhere(['IN', 'foreign_event_participants.sex', $sex]);
    }

    public function filterByGroups(ActiveQuery $query, array $groupIds)
    {
        return $query->andWhere(['IN', 'training_group_id', $groupIds]);
    }
}