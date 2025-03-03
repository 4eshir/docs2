<?php

namespace frontend\services\educational;

use common\repositories\educational\CertificateRepository;
use frontend\forms\certificate\CertificateForm;
use frontend\models\work\educational\CertificateWork;

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
        $currentNumber = $this->repository->getCurrentCertificateNumber();
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
}