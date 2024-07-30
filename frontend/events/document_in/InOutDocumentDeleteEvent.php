<?php

namespace frontend\events\document_in;

use common\models\work\document_in_out\DocumentInWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use frontend\events\EventInterface;
use Yii;

class InOutDocumentDeleteEvent implements EventInterface
{
    private $documentInId;
    private InOutDocumentsRepository $repository;

    public function __construct($documentInId)
    {
        $this->documentInId = $documentInId;
        $this->repository = Yii::createObject(InOutDocumentsRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        if ($this->repository->getByDocumentInId($this->documentInId)) {
            return [
                $this->repository->prepareDelete($this->documentInId)
            ];
        }
        else {
            return [];
        }
    }
}