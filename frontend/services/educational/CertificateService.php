<?php

namespace frontend\services\educational;

use common\helpers\files\FilesHelper;
use common\repositories\educational\CertificateRepository;
use frontend\components\wizards\CertificateWizard;
use frontend\forms\certificate\CertificateForm;
use frontend\models\work\educational\CertificateWork;
use Yii;
use yii\helpers\FileHelper;

class CertificateService
{
    private CertificateRepository $repository;

    public function __construct(
        CertificateRepository $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Функция сохранения сертификатов в БД
     *
     * @param CertificateForm $form
     * @return int[]
     */
    public function saveAllCertificates(CertificateForm $form)
    {
        $currentNumber = $this->getCurrentCertificateNumber();
        $ids = [];
        if (is_array($form->participants)) {
            foreach ($form->participants as $participantId) {
                $ids[] = $this->repository->save(
                    CertificateWork::fill(
                        $currentNumber,
                        $form->templateId,
                        $participantId,
                        CertificateWork::STATUS_CREATE
                    )
                );
                $currentNumber++;
            }
        }

        return $ids;
    }

    private function getCurrentCertificateNumber()
    {
        return $this->repository->getCount() + 1;
    }

    public function uploadCertificates(array $certificateIds)
    {
        FilesHelper::createDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/');
        foreach ($certificateIds as $id) {
            /** @var CertificateWork $certificate */
            $certificate = $this->repository->get($id);
            $participant = $certificate->trainingGroupParticipantWork;
            CertificateWizard::DownloadCertificate($certificate, $participant, CertificateWizard::DESTINATION_SERVER);
        }
    }

    public function downloadCertificates()
    {
        CertificateWizard::archiveDownload();
        FilesHelper::removeDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/');
    }
}