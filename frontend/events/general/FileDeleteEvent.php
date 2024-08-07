<?php

namespace frontend\events\general;

use common\events\EventInterface;
use common\models\work\general\FilesWork;
use common\repositories\general\FilesRepository;
use DomainException;
use Yii;

class FileDeleteEvent implements EventInterface
{
    private $fileId;
    private FilesRepository $repository;

    public function __construct(
        $fileId
    )
    {
        $this->fileId = $fileId;
        $this->repository = Yii::createObject(FilesRepository::class);
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        return [
            $this->repository->prepareDelete($this->fileId)
        ];
    }
}