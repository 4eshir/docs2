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
    public function addActParticipantEvent(OrderEventForm $model, $post, $foreignEventId)
    {

        //



        $nomination = $post['nominations'];
        $participantId = $post['OrderEventForm']['participant_id'];
        $teacherId = $post['OrderEventForm']['teacher_id'];
        $teacher2Id = $post['OrderEventForm']['teacher2_id'];
        $branch = $post['OrderEventForm']['branch'];
        $focus = $post['OrderEventForm']['focus'];
        $eventWays = $post['OrderEventForm']['formRealization'];
        $actTeamList = $post['OrderEventForm']['teamList'];
        $actNominationsList = $post['OrderEventForm']['nominationList'];
        $teamNameId = [];
        $type = [];
        $allowRemote = [];

        //
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
                            $teacherId,
                            $teacher2Id,
                            $teamNameId,
                            $foreignEventId,
                            $branch,
                            $focus,
                            $type,
                            $allowRemote,
                            $nomination
                        ),
                            ActParticipant::class
                        );
                    }
            }
        }
    }
}