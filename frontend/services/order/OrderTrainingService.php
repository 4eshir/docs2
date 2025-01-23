<?php

namespace app\services\order;

use app\events\educational\training_group\CreateOrderTrainingGroupParticipantEvent;
use app\events\educational\training_group\DeleteOrderTrainingGroupParticipantEvent;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\OrderTrainingWork;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\services\general\files\FileService;
use frontend\events\educational\training_group\CreateTrainingGroupParticipantEvent;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class OrderTrainingService
{
    private FileService $fileService;
    private OrderMainFileNameGenerator $filenameGenerator;
    private OrderMainService $orderMainService;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator,
        OrderMainService $orderMainService,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        TrainingGroupRepository $trainingGroupRepository

    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->orderMainService = $orderMainService;
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
            $addArray = array_diff($formRespPeople, $respPeople);
            $deleteArray = array_diff($respPeople, $formRespPeople);
        }
        else if($formRespPeople == NULL && $respPeople != NULL) {
            $deleteArray = $respPeople;
            $addArray = NULL;
        }
        else if($respPeople == NULL && $formRespPeople != NULL) {
            $addArray = $formRespPeople;
            $deleteArray = NULL;
        }
        else {
            $deleteArray = NULL;
            $addArray = NULL;
        }
        if($deleteArray != NULL) {
            $this->orderMainService->deleteOrderPeopleEvent($deleteArray, $model);
        }
        if($addArray != NULL) {
            $this->orderMainService->addOrderPeopleEvent($addArray, $model);
        }
        $model->releaseEvents();
    }
    public function getGroupsEmptyDataProvider()
    {
       return new ActiveDataProvider([
           'query' => $this->trainingGroupRepository->empty()
       ]);
    }
    public function getParticipantEmptyDataProvider()
    {
        return new ActiveDataProvider([
            'query' => $this->trainingGroupParticipantRepository->empty()
        ]);
    }
    public function getGroupsDataProvider(OrderTrainingWork $model)
    {
        return new ActiveDataProvider([
            'query' => $this->trainingGroupRepository->getByBranchQuery($model->branch)
        ]);
    }
    public function getParticipantsDataProvider(OrderTrainingWork $model)
    {
        $status = $this->getStatus($model);
        if($status == 1) {
            $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()->where(['order_id' => $model->id])->all(),
                'training_group_participant_in_id');
        }
        if($status == 2) {
            $orderParticipantId = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()->where(['order_id' => $model->id])->all(),
                'training_group_participant_out_id');
        }
        if($status == 2){
            //code
        }
        $groupId = ArrayHelper::getColumn(TrainingGroupParticipantWork::find()->where(['id' => $orderParticipantId])->all(),
            'training_group_id'
        );
        $query = TrainingGroupParticipantWork::find()
            ->orWhere(['id' => $orderParticipantId])
            ->orWhere(['and', ['training_group_id' => $groupId], ['status' => $status - 1]]);
        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
    public function createOrderTrainingGroupParticipantEvent(OrderTrainingWork $model, $status, $post){
        $participantIds = $post['group-participant-selection'];
        if($status == 1) {
            if ($participantIds != NULL) {
                foreach ($participantIds as $participantId) {
                    $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent(NULL, $participantId, $model->id),
                        OrderTrainingWork::class);
                    $this->trainingGroupParticipantRepository->setStatus($participantId, $status);
                }
            }
        }
        if($status == 2) {
            if ($participantIds != NULL) {
                foreach ($participantIds as $participantId) {
                    $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($participantId, NULL, $model->id),
                        OrderTrainingWork::class);
                    $this->trainingGroupParticipantRepository->setStatus($participantId, $status);
                }
            }
        }
        if($status == 3) {
            $transferGroupIds = $post['transfer-group'];
            var_dump($participantIds, $transferGroupIds);
            if($participantIds != NULL){
                foreach ($participantIds as $participantId) {
                    if($transferGroupIds[$participantId] != NULL) {
                        //create new TrainingGroupParticipant
                        $newTrainingGroupParticipant = TrainingGroupParticipantWork::fill(
                            $transferGroupIds[$participantId],
                            $participantId,
                            NULL
                        );
                        $newTrainingGroupParticipant->setStatus($status - 1);
                        $this->trainingGroupParticipantRepository->save($newTrainingGroupParticipant);
                        $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($participantId, $transferGroupIds[$participantId], $newTrainingGroupParticipant->id),
                            OrderTrainingWork::class);
                        //update old TrainingGroupParticipant
                        $this->trainingGroupParticipantRepository->setStatus($participantId, $status - 2);
                    }
                }
            }
        }

    }
    public function deleteOrderTrainingGroupParticipantEvent(OrderTrainingWork $model, $id){
    }
    public function updateOrderTrainingGroupParticipantEvent(OrderTrainingWork $model, $status, $post){
        $trainingGroupParticipants = $post['group-participant-selection'];
        if ($status == 1) {
            $formParticipants = $trainingGroupParticipants;
            $existsParticipants = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
                ->andWhere(['order_id' => $model->id])
                ->andWhere(['training_group_participant_out_id' => NULL])
                ->all(),
                'training_group_participant_in_id');
            if($formParticipants == NULL && $existsParticipants == NULL){
                $deleteParticipants = NULL;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $existsParticipants == NULL) {
                $deleteParticipants = NULL;
                $createParticipants = $formParticipants;
            }
            else if($formParticipants == NULL && $existsParticipants != NULL) {
                $deleteParticipants = $existsParticipants;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $formParticipants != NULL){
                $deleteParticipants = array_diff($existsParticipants, $formParticipants);
                $createParticipants = array_diff($formParticipants, $existsParticipants);
            }
            if ($deleteParticipants != NULL) {
                foreach ($deleteParticipants as $deleteParticipant) {
                    $model->recordEvent(new DeleteOrderTrainingGroupParticipantEvent(NULL, $deleteParticipant, $model->id), OrderTrainingGroupParticipantWork::class);
                    //old status
                    $this->trainingGroupParticipantRepository->setStatus( $deleteParticipant, 0);
                }
            }
            if ($createParticipants != NULL) {
                foreach ($createParticipants as $createParticipant) {
                    $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent(NULL, $createParticipant, $model->id), OrderTrainingGroupParticipantWork::class);
                    $this->trainingGroupParticipantRepository->setStatus($createParticipant, 1);
                }
            }
        }
        if ($status == 2){
            $formParticipants = $trainingGroupParticipants;
            $existsParticipants = ArrayHelper::getColumn(OrderTrainingGroupParticipantWork::find()
                ->andWhere(['order_id' => $model->id])
                ->andWhere(['training_group_participant_in_id' => NULL])
                ->all(),
                'training_group_participant_out_id');
            if($formParticipants == NULL && $existsParticipants == NULL){
                $deleteParticipants = NULL;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $existsParticipants == NULL) {
                $deleteParticipants = NULL;
                $createParticipants = $formParticipants;
            }
            else if($formParticipants == NULL && $existsParticipants != NULL) {
                $deleteParticipants = $existsParticipants;
                $createParticipants = NULL;
            }
            else if($formParticipants != NULL && $formParticipants != NULL){
                $deleteParticipants = array_diff($existsParticipants, $formParticipants);
                $createParticipants = array_diff($formParticipants, $existsParticipants);
            }
            if ($deleteParticipants != NULL) {
                foreach ($deleteParticipants as $deleteParticipant) {
                    $model->recordEvent(new DeleteOrderTrainingGroupParticipantEvent($deleteParticipant, NULL, $model->id), OrderTrainingGroupParticipantWork::class);
                    //old status
                    $this->trainingGroupParticipantRepository->setStatus( $deleteParticipant, 1);
                }
            }
            if ($createParticipants != NULL) {
                foreach ($createParticipants as $createParticipant) {
                    $model->recordEvent(new CreateOrderTrainingGroupParticipantEvent($createParticipant,NULL, $model->id), OrderTrainingGroupParticipantWork::class);
                    $this->trainingGroupParticipantRepository->setStatus($createParticipant, 2);
                }
            }
        }
        if($status == 3) {


        }
    }
}
