<?php

namespace frontend\services\regulation;

use common\helpers\files\filenames\DocumentInFileNameGenerator;
use common\helpers\files\filenames\RegulationFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\regulation\RegulationWork;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use yii\web\UploadedFile;

class RegulationService
{
    private FileService $fileService;
    private RegulationFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        RegulationFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(RegulationWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
    }

    public function saveFilesFromModel(RegulationWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);

            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => RegulationWork::tableName(),
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
    }
}