<?php

namespace frontend\models\work\educational;

use common\models\scaffold\Certificate;
use frontend\models\work\CertificateTemplatesWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;

/**
 * @property CertificateTemplatesWork $certificateTemplatesWork
 * @property TrainingGroupParticipantWork $trainingGroupParticipantWork
 */
class CertificateWork extends Certificate
{
    public function getCertificateTemplatesWork()
    {
        return $this->hasOne(CertificateTemplatesWork::class, ['id' => 'certificate_template_id']);
    }

    public function getTrainingGroupParticipantWork()
    {
        return $this->hasOne(TrainingGroupParticipantWork::class, ['id' => 'training_group_participant_id']);
    }
}