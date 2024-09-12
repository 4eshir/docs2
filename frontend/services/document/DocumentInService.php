<?php

namespace frontend\services\document;

use common\helpers\files\filenames\DocumentInFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\web\UploadedFile;

class DocumentInService implements DatabaseService
{
    private FileService $fileService;
    private DocumentInFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        DocumentInFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(DocumentInWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }

    public function saveFilesFromModel(DocumentInWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);

            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => DocumentInWork::tableName(),
                    'fileType' => FilesHelper::TYPE_SCAN
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_SCAN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->docFiles[$i - 1],
                $filename,
                [
                    'tableName' => DocumentInWork::tableName(),
                    'fileType' => FilesHelper::TYPE_DOC
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_DOC,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->appFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_APP, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->appFiles[$i - 1],
                $filename,
                [
                    'tableName' => DocumentInWork::tableName(),
                    'fileType' => FilesHelper::TYPE_APP
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_APP,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
    }

    public function isAvailableDelete($id)
    {
        return [];
    }
}