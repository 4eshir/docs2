<?php

namespace frontend\components\wizards;

use common\helpers\common\BaseFunctions;
use common\helpers\files\FilePaths;
use common\helpers\html\CertificateBuilder;
use frontend\helpers\CertificateHelper;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use kartik\mpdf\Pdf;
use Yii;

class CertificateWizard
{
    // Места итоговой загрузки сгенерированных сертификатов
    const DESTINATION_DOWNLOAD = 1;
    const DESTINATION_SERVER = 2;

    public static function DownloadCertificate(CertificateWork $certificate, TrainingGroupParticipantWork $participant, int $destination, string $path = null)
    {
        if (strripos($certificate->certificateTemplatesWork->name, CertificateWork::TECHNOSUMMER)) {
            if (
                strripos($certificate->certificateTemplatesWork->name, CertificateWork::INTENSIVE) ||
                strripos($certificate->certificateTemplatesWork->name, CertificateWork::PRO)
            ) {
                $mpdf = CertificateWizard::CertificateIntensive($certificate, $participant);
            }
            else {
                $mpdf = CertificateWizard::CertificateTechnosummer($certificate, $participant);
            }
        }
        else if (strripos($certificate->certificateTemplatesWork->name, CertificateWork::SCHOOL)) {
            $mpdf = CertificateWizard::CertificateSchool($certificate, $participant);
        }
        else {
            $mpdf = CertificateWizard::CertificateStandard($certificate, $participant);
        }

        if ($destination === self::DESTINATION_DOWNLOAD) {
            $mpdf->Output(
                'Сертификат №'. $certificate->getCertificateLongNumber() . ' '.
                $participant->participantWork->getFIO(PersonInterface::FIO_FULL) .'.pdf',
                'D'
            );
            exit;
        }
        else {
            $certificateName = 'Certificate #'.
                $certificate->getCertificateLongNumber() . ' '.
                BaseFunctions::rus2EngTranslit($participant->participantWork->getFIO(PersonInterface::FIO_FULL));
            if (is_null($path)) {
                $mpdf->Output(Yii::$app->basePath.'/download/'. Yii::$app->user->identity->getId().'/'. $certificateName . '.pdf', 'F'); // call the mpdf api output as needed
            }
            else {
                $mpdf->Output($path . $certificateName . '.pdf', 'F');
            }

            return $certificateName;
        }
    }

    private static function CertificateStandard(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $trainedText = CertificateHelper::getMainText($participant, $genderVerbs);
        $size = CertificateHelper::getTextSize(strlen($trainedText));

        $content = CertificateBuilder::createStandardCertificate($certificate, $participant, $size, $trainedText);
        return CertificateBuilder::createPdfClass($content);
    }

    private static function CertificateSchool(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $content = CertificateBuilder::createSchoolCertificate($certificate, $participant, $genderVerbs);
        return CertificateBuilder::createPdfClass($content);
    }

    private static function CertificateTechnosummer(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $content = CertificateBuilder::createTechnosummerCertificate($certificate, $participant);
        return CertificateBuilder::createPdfClass($content);
    }

    private static function CertificateIntensive(CertificateWork $certificate, TrainingGroupParticipantWork $participant)
    {
        $genderVerbs = CertificateHelper::getGenderVerbs($participant->participantWork);

        $content = CertificateBuilder::createIntensiveCertificate($certificate, $participant, $genderVerbs);
        return CertificateBuilder::createPdfClass($content);
    }
}