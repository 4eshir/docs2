<?php

namespace frontend\services\event;

use common\helpers\files\filenames\EventNameGenerator;
use common\helpers\files\FilesHelper;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\event\EventWork;
use yii\web\UploadedFile;

class EventService implements DatabaseService
{
    private FileService $fileService;
    private EventNameGenerator $filenameGenerator;

    public function __construct(
        EventNameGenerator $filenameGenerator,
        FileService $fileService
    )
    {
        $this->filenameGenerator = $filenameGenerator;
        $this->fileService = $fileService;
    }

    public function getFilesInstances(EventWork $model)
    {
        $model->protocolFiles = UploadedFile::getInstances($model, 'protocolFiles');
        $model->reportingFiles = UploadedFile::getInstances($model, 'reportingFiles');
        $model->photoFiles = UploadedFile::getInstances($model, 'photoFiles');
        $model->otherFiles = UploadedFile::getInstances($model, 'otherFiles');
    }

    public function saveFilesFromModel(EventWork $model)
    {
        for ($i = 1; $i < count($model->protocolFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_PROTOCOL, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->protocolFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PROTOCOL
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_PROTOCOL,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->reportingFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_REPORT, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->reportingFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_REPORT
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_REPORT,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->photoFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_PHOTO, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->photoFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PHOTO
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_PHOTO,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->otherFiles) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_OTHER, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->otherFiles[$i - 1],
                $filename,
                [
                    'tableName' => EventWork::tableName(),
                    'fileType' => FilesHelper::TYPE_OTHER
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_OTHER,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }
    }

    public function isAvailableDelete($id)
    {
        // TODO: Implement isAvailableDelete() method.
    }
}