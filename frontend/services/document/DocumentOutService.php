<?php

namespace frontend\services\document;

use common\helpers\files\filenames\DocumentOutFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\document_in_out\DocumentOutWork;
use yii\web\UploadedFile;

class DocumentOutService implements DatabaseService
{
    private FileService $fileService;
    private DocumentOutFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        DocumentOutFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(DocumentOutWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }

    public function saveFilesFromModel(DocumentOutWork $model)
    {
        if ($model->scanFile !== null) {
            $filepath = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);

            $this->fileService->uploadFile(
                $model->scanFile,
                $filepath
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_SCAN,
                    $filepath,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
            $filepath = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->docFiles[$i - 1],
                $filepath
            );
        }

        for ($i = 1; $i < count($model->appFiles) + 1; $i++) {
            $filepath = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_APP, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->appFiles[$i - 1],
                $filepath
            );
        }
    }

    public function isAvailableDelete($id)
    {
        return [];
    }
}