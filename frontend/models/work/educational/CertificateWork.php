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
    const TECHNOSUMMER = 'лето';
    const INTENSIVE = 'Интенсив';
    const PRO = 'ПРО';
    const SCHOOL = 'школа';
    const PLUS = 'Плюс';

    const STATUS_CREATE = 0;
    const STATUS_SEND = 1;


    public static function fill(
        string $certificateNumber,
        int $templateId,
        int $participantId,
        int $status
    )
    {
        $entity = new static();
        $entity->certificate_number = $certificateNumber;
        $entity->certificate_template_id = $templateId;
        $entity->training_group_participant_id = $participantId;
        $entity->status = $status;

        return $entity;
    }

    public function getCertificateLongNumber()
    {
        return sprintf('%06d', $this->certificate_number);
    }

    public function getCertificateTemplatesWork()
    {
        return $this->hasOne(CertificateTemplatesWork::class, ['id' => 'certificate_template_id']);
    }

    public function getTrainingGroupParticipantWork()
    {
        return $this->hasOne(TrainingGroupParticipantWork::class, ['id' => 'training_group_participant_id']);
    }
}