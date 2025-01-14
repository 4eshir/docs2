<?php

namespace common\repositories\providers\group_participant;

use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

interface TrainingGroupParticipantProviderInterface
{
    public function get($id);
    public function getParticipantsFromGroup($groupId);
    public function delete(TrainingGroupParticipantWork $model);
    public function save(TrainingGroupParticipantWork $model);
}