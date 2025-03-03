<?php

namespace common\components\wizards;

use frontend\models\work\educational\CertificateWork;

class PdfWizard
{
    public static function DownloadCertificate(CertificateWork $certificate, string $destination, $path = null)
    {
        if (strripos($certificate->certificateTemplatesWork->name, CertificateWork::TECHNOSUMMER)) {
            if (
                strripos($certificate->certificateTemplatesWork->name, CertificateWork::INTENSIVE) ||
                strripos($certificate->certificateTemplatesWork->name, CertificateWork::PRO)
            ) {
                return PdfWizard::CertificatIntensives($certificat_id, $destination, $path);
            }
            else {
                return PdfWizard::CertificatTechnoSummer($certificat_id, $destination, $path);
            }
        }
        else if (strripos($certificate->certificateTemplatesWork->name, CertificateWork::SCHOOL))
            return PdfWizard::CertificatSchool($certificat_id, $destination, $path);
        else
            return PdfWizard::CertificatStandard($certificat_id, $destination, $path);
    }
}