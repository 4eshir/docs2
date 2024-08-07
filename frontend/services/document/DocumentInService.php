<?php

namespace frontend\services\document;

use common\helpers\files\filenames\DocumentInFileNameGenerator;
use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\work\document_in_out\DocumentInWork;
use common\repositories\general\CompanyRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\PositionRepository;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use yii\web\UploadedFile;

class DocumentInService
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
}