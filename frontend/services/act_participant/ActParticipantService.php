<?php

namespace app\services\act_participant;

use app\events\act_participant\ActParticipantCreateEvent;
use app\models\work\team\ActParticipantWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\models\scaffold\ActParticipant;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\forms\OrderEventForm;

class ActParticipantService
{
    public function addActParticipantEvent(OrderEventForm $model, $participantId, $teacherId, $teacher2Id, $foreignEventId, $branch, $focus, $allowRemoteId, $nomination)
    {
        if($participantId!= NULL) {
            if (
                count($teacherId) == count($teacher2Id)
                && count($teacher2Id) == count($branch)
                && count($branch) == count($focus)
                && count($focus) == count($nomination)
            ) {
                for ($i = 0; $i < count($teacherId); $i++)
                    if ($participantId[$i] != NULL) {
                        $model->recordEvent(new ActParticipantCreateEvent(
                            $participantId[$i],
                            $teacherId[$i],
                            $teacher2Id[$i],
                            $foreignEventId,
                            $branch[$i],
                            $focus[$i],
                            $allowRemoteId[$i],
                            $nomination[$i]),
                            ActParticipant::class
                        );
                    }
            }
        }
    }
}