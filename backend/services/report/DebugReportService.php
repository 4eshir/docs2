<?php

namespace backend\services\report;

use backend\helpers\DebugReportHelper;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

class DebugReportService
{
    /**
     * @param TrainingGroupParticipantWork[] $participants
     * @return string[][]
     */
    public function createParticipantDebugData(array $participants): array
    {
        $data = [];
        foreach ($participants as $participant) {
            $data[] = DebugReportHelper::createParticipantsDataCsv($participant);
        }

        return $data;
    }
}