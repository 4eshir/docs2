<?php

namespace frontend\services\responsibility;

use common\helpers\files\filenames\ResponsibilityFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\web\UploadedFile;

class LocalResponsibilityService implements DatabaseService
{
    private FileService $fileService;
    private ResponsibilityFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        ResponsibilityFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(LocalResponsibilityWork $model)
    {
        $model->filesList = UploadedFile::getInstances($model, 'filesList');
    }

    public function saveFilesFromModel(LocalResponsibilityWork $model)
    {
        for ($i = 1; $i < count($model->filesList) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_OTHER, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->filesList[$i - 1],
                $filename,
                [
                    'tableName' => LocalResponsibilityWork::tableName(),
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