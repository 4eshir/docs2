<?php

namespace app\services\order;

use app\models\work\general\OrderPeopleWork;
use app\models\work\order\OrderTrainingWork;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\OrderPeopleRepository;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\events\general\OrderPeopleDeleteEvent;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class OrderTrainingService
{
    private FileService $fileService;
    private OrderMainFileNameGenerator $filenameGenerator;
    private OrderMainService $orderMainService;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator,
        OrderMainService $orderMainService,
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        TrainingGroupRepository $trainingGroupRepository
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->orderMainService = $orderMainService;
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;

    }
    public function setBranch(OrderTrainingWork $model)
    {
        $number = $model->order_number;
        $parts = explode("/", $number);
        $nomenclature = $parts[0];
        $model->setBranch(NomenclatureDictionary::getBranchByNomenclature($nomenclature));
    }
    public function getStatus(OrderTrainingWork $model)
    {
        $number = $model->order_number;
        $parts = explode("/", $number);
        $nomenclature = $parts[0];
        return NomenclatureDictionary::getStatus($nomenclature);
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
    public function updateTrainingGroupParticipantStatus($participants, $status)
    {
        foreach ($participants as $participant) {
            $model = TrainingGroupParticipantWork::findOne($participant);
            $model->status = $status;
            $model->save();
        }
    }
}