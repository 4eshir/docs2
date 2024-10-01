<?php

namespace frontend\services\educational;

use common\components\wizards\ExcelWizard;
use common\helpers\files\filenames\TrainingProgramFileNameGenerator;
use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use frontend\events\educational\training_program\CreateThemeInPlanEvent;
use frontend\events\educational\training_program\ResetThematicPlanEvent;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\educational\ThematicPlanWork;
use frontend\models\work\educational\TrainingProgramWork;
use Yii;
use yii\web\UploadedFile;

class TrainingProgramService implements DatabaseService
{
    private FileService $fileService;
    private TrainingProgramFileNameGenerator $filenameGenerator;

    public function __construct(FileService $fileService, TrainingProgramFileNameGenerator $filenameGenerator)
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getFilesInstances(TrainingProgramWork $model)
    {
        $model->mainFile = UploadedFile::getInstance($model, 'mainFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
        $model->contractFile = UploadedFile::getInstance($model, 'contractFile');
        $model->utpFile = UploadedFile::getInstance($model, 'utpFile');
    }

    public function saveUtpFromFile(TrainingProgramWork $model)
    {
        if ($model->utpFile !== null) {
            $model->recordEvent(new ResetThematicPlanEvent($model->id), ThematicPlanWork::class);

            $newFilename = StringFormatter::createHash(date("Y-m-d H:i:s")) . '.' . $model->utpFile->extension;
            $this->fileService->uploadFile($model->utpFile, $newFilename, ['filepath' => FilePaths::TEMP_FILEPATH . '/']);
            $data = ExcelWizard::getDataFromColumns(
                Yii::$app->basePath . FilePaths::TEMP_FILEPATH . '/' . $newFilename,
                ['Тема', 'Тип контроля']
            );

            for ($i = 0; $i < count($data['Тема']); $i++) {
                $model->recordEvent(new CreateThemeInPlanEvent($data['Тема'][$i], $model->id, $data['Тип контроля'][$i]), ThematicPlanWork::class);
            }

            $this->fileService->deleteFile(FilePaths::TEMP_FILEPATH . '/' . $newFilename);
        }
    }

    public function saveFilesFromModel(TrainingProgramWork $model)
    {
        if ($model->mainFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_MAIN);

            $this->fileService->uploadFile(
                $model->mainFile,
                $filename,
                [
                    'tableName' => TrainingProgramWork::tableName(),
                    'fileType' => FilesHelper::TYPE_MAIN
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_MAIN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }

        if ($model->contractFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_CONTRACT);

            $this->fileService->uploadFile(
                $model->contractFile,
                $filename,
                [
                    'tableName' => TrainingProgramWork::tableName(),
                    'fileType' => FilesHelper::TYPE_CONTRACT
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_CONTRACT,
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
                    'tableName' => TrainingProgramWork::tableName(),
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
    }

    public function isAvailableDelete($id)
    {
        return [];
    }
}