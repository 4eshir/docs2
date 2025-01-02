<?php

namespace app\services\order;

use app\models\work\general\OrderPeopleWork;
use app\models\work\order\OrderTrainingWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\repositories\general\OrderPeopleRepository;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\events\general\OrderPeopleDeleteEvent;
use yii\web\UploadedFile;

class OrderTrainingService
{
    private FileService $fileService;
    private OrderMainFileNameGenerator $filenameGenerator;
    private OrderMainService $orderMainService;
    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator,
        OrderMainService $orderMainService
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->orderMainService = $orderMainService;
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
    public function updateOrderPeopleEvent($respPeople, $formRespPeople , OrderTrainingWork $model)
    {
        if($respPeople != NULL && $formRespPeople != NULL) {
            $addSquadParticipant = array_diff($formRespPeople, $respPeople);
            $deleteSquadParticipant = array_diff($respPeople, $formRespPeople);
        }
        else if($formRespPeople == NULL && $respPeople != NULL) {
            $deleteSquadParticipant = $respPeople;
            $addSquadParticipant = NULL;
        }
        else if($respPeople == NULL && $formRespPeople != NULL) {
            $addSquadParticipant = $formRespPeople;
            $deleteSquadParticipant = NULL;
        }
        else {
            $deleteSquadParticipant = NULL;
            $addSquadParticipant = NULL;
        }
        if($deleteSquadParticipant != NULL) {
            $this->orderMainService->deleteOrderPeopleEvent($deleteSquadParticipant, $model);
        }
        if($addSquadParticipant != NULL) {
            $this->orderMainService->addOrderPeopleEvent($addSquadParticipant, $model);
        }
        $model->releaseEvents();
    }
}