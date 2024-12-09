<?php

namespace app\services\order;

use app\models\work\order\OrderEventWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\forms\OrderEventForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\Url;
class OrderEventService
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
    public function saveFilesFromModel(OrderEventWork $model)
    {
        if ($model->scanFile != null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);
            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => OrderEventWork::tableName(),
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
                        'tableName' => OrderEventWork::tableName(),
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