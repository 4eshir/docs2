<?php
namespace app\services\order;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\ExpireWork;
use app\models\work\order\OrderMainWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\services\general\files\FileService;
use frontend\events\expire\ExpireCreateEvent;
use frontend\events\general\FileCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\web\UploadedFile;

class OrderMainService {
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
    public function getFilesInstances(OrderMainWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }
    public function addExpireEvent($docs, $regulation, $model) {
        if($docs[0] != NULL && $regulation[0] != NULL){
            for($i = 0; $i < count($docs); $i++){
                $model->recordEvent(new ExpireCreateEvent($regulation[$i],
                    $regulation[$i],$docs[$i],1,1), ExpireWork::class);
            }
        }
    }
    public function addOrderPeopleEvent($respPeople, $model)
    {
        if ($respPeople[0] != NULL) {
            for ($i = 0; $i < count($respPeople); $i++) {
                $model->recordEvent(new OrderPeopleCreateEvent($respPeople[$i], $model->id), OrderPeopleWork::class);
            }
        }
    }
    public function saveFilesFromModel(OrderMainWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);
            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => OrderMainWork::tableName(),
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
                        'tableName' => OrderMainWork::tableName(),
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