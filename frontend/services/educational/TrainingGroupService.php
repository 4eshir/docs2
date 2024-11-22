<?php

namespace frontend\services\educational;

use common\helpers\files\filenames\TrainingGroupFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\web\UploadedFile;

class TrainingGroupService
{
    private FileService $fileService;
    private TrainingGroupFileNameGenerator $filenameGenerator;

    public function __construct(FileService $fileService, TrainingGroupFileNameGenerator $filenameGenerator)
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function convertBaseFormToModel(TrainingGroupBaseForm $form)
    {
        $entity = new TrainingGroupWork();
        $entity->branch = $form->branch;
        $entity->training_program_id = $form->trainingProgramId;
        $entity->budget = $form->budget;
        $entity->is_network = $form->network;
        $entity->start_date = $form->startDate;
        $entity->finish_date = $form->endDate;
        $entity->order_stop = $form->endLoadOrders;

        return $entity;
    }

    public function getFilesInstances(TrainingGroupBaseForm $form)
    {
        $form->photos = UploadedFile::getInstances($form, 'photos');
        $form->presentations = UploadedFile::getInstances($form, 'presentations');
        $form->workMaterials = UploadedFile::getInstances($form, 'workMaterials');
    }

    public function saveFilesFromModel(TrainingGroupBaseForm $form)
    {
        for ($i = 1; $i < count($form->photos) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_PHOTO, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->photos[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PHOTO
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_PHOTO,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }

        for ($i = 1; $i < count($form->presentations) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_PRESENTATION, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->presentations[$i - 1],
                $filename,
                [
                    'tableName' => DocumentInWork::tableName(),
                    'fileType' => FilesHelper::TYPE_PRESENTATION
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_PRESENTATION,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }

        for ($i = 1; $i < count($form->workMaterials) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($form, FilesHelper::TYPE_WORK, ['counter' => $i]);

            $this->fileService->uploadFile(
                $form->workMaterials[$i - 1],
                $filename,
                [
                    'tableName' => TrainingGroupWork::tableName(),
                    'fileType' => FilesHelper::TYPE_WORK
                ]
            );

            $form->recordEvent(
                new FileCreateEvent(
                    TrainingGroupWork::tableName(),
                    $form->id,
                    FilesHelper::TYPE_WORK,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                TrainingGroupWork::tableName()
            );
        }
    }
}