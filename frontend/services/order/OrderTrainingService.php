<?php

namespace app\services\order;

use app\models\work\general\OrderPeopleWork;
use app\models\work\order\OrderTrainingWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use yii\web\UploadedFile;

class OrderTrainingService
{
    private FileService $fileService;
    private OrderMainFileNameGenerator $filenameGenerator;
    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
    }
    public function createOrderPeopleArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var OrderPeopleWork $item */
            $result[] = $item->getFullFio();
        }
        return $result;
    }
    public function getFilesInstances(OrderTrainingWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }
    public function saveFilesFromModel(OrderTrainingWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);
            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => OrderTrainingWork::tableName(),
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
        if ($model->docFiles != NULL) {
            for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
                $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

                $this->fileService->uploadFile(
                    $model->docFiles[$i - 1],
                    $filename,
                    [
                        'tableName' => OrderTrainingWork::tableName(),
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
    }
}