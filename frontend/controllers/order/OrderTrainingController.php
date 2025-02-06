<?php

namespace frontend\controllers\order;

use app\components\DynamicWidget;
use frontend\components\GroupParticipantWidget;
use frontend\models\search\SearchOrderTraining;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderTrainingWork;
use frontend\services\order\DocumentOrderService;
use frontend\services\order\OrderPeopleService;
use frontend\services\order\OrderTrainingService;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\order\OrderTrainingRepository;
use common\services\general\files\FileService;
use DomainException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OrderTrainingController extends DocumentController
{
    private PeopleStampRepository $peopleStampRepository;
    private DocumentOrderService $documentOrderService;
    private OrderTrainingService $orderTrainingService;
    private OrderPeopleRepository $orderPeopleRepository;
    private OrderPeopleService $orderPeopleService;
    private OrderTrainingRepository $orderTrainingRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    private LockWizard $lockWizard;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;

    public function __construct(
        $id,
        $module,
        PeopleStampRepository $peopleStampRepository,
        DocumentOrderService $documentOrderService,
        OrderTrainingService $orderTrainingService,
        OrderPeopleRepository $orderPeopleRepository,
        OrderPeopleService $orderPeopleService,
        OrderTrainingRepository $orderTrainingRepository,
        TrainingGroupRepository $trainingGroupRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        LockWizard $lockWizard,

        FileService $fileService,
        FilesRepository $filesRepository,
        $config = []
    )
    {
        $this->peopleStampRepository = $peopleStampRepository;
        $this->documentOrderService = $documentOrderService;
        $this->orderTrainingService = $orderTrainingService;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->orderPeopleService = $orderPeopleService;
        $this->orderTrainingRepository = $orderTrainingRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->lockWizard = $lockWizard;
        $this->trainingGroupParticipantRepository = $trainingGroupParticipantRepository;
        parent::__construct($id, $module, $fileService, $filesRepository, $config);
    }
    public function actionIndex(){
        $searchModel = new SearchOrderTraining();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id){
        $modelResponsiblePeople = implode('<br>',
            $this->documentOrderService->createOrderPeopleArray(
                $this->orderPeopleRepository->getResponsiblePeople($id)
            )
        );
        return $this->render('view', [
            'model' => $this->orderTrainingRepository->get($id),
            'modelResponsiblePeople' => $modelResponsiblePeople,
        ]);
    }
    public function actionCreate(){
        $model = new OrderTrainingWork();
        $people = $this->peopleStampRepository->getAll();
        $post = Yii::$app->request->post();
        $groups = $this->orderTrainingService->getGroupsEmptyDataProvider();
        $groupParticipant = $this->orderTrainingService->getParticipantEmptyDataProvider();;
        if ($model->load($post)) {
            if (!$model->validate()) {
               throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $respPeopleId = DynamicWidget::getData(basename(OrderTrainingWork::class), "responsible_id", $post);
            $this->documentOrderService->getFilesInstances($model);
            $model->generateOrderNumber();
            $this->orderTrainingRepository->save($model);
            $status = $this->orderTrainingService->getStatus($model);
            //create
            $this->orderTrainingService->createOrderTrainingGroupParticipantEvent($model, $status, $post);
            //create
            $this->documentOrderService->saveFilesFromModel($model);
            $this->orderPeopleService->addOrderPeopleEvent($respPeopleId, $model);
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people,
            'groups' => $groups,
            'groupParticipant' => $groupParticipant,
            'groupCheckOption' => [],
            'groupParticipantOption' => [],

        ]);
    }
    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, DocumentOrderWork::tableName(), Yii::$app->user->id)) {
            $model = $this->orderTrainingRepository->get($id);
            $this->orderTrainingService->setBranch($model);
            $people = $this->peopleStampRepository->getAll();
            $model->responsible_id = ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id');
            $post = Yii::$app->request->post();
            $number = $model->order_number;
            $groups = $this->orderTrainingService->getGroupsDataProvider($model);
            $groupParticipant = $this->orderTrainingService->getParticipantsDataProvider($model);
            $transferGroups = $this->trainingGroupRepository->getByBranchQuery($model->branch)->all();
            $tables = $this->documentOrderService->getUploadedFilesTables($model);
            $status = $this->orderTrainingService->getStatus($model);
            $groupCheckOption = $this->trainingGroupRepository->getAttachedGroupsByOrder($id, $status);
            $groupParticipantOption = $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($id, $status);
            if ($model->load($post)) {
                $this->lockWizard->unlockObject($id, DocumentOrderWork::tableName());
                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }

                $this->documentOrderService->getFilesInstances($model);
                $model->order_number = $number;
                $this->orderTrainingRepository->save($model);
                //$status = $this->orderTrainingService->getStatus($model);
                //update
                $this->orderTrainingService->updateOrderTrainingGroupParticipantEvent($model, $status, $post);
                //update
                $this->documentOrderService->saveFilesFromModel($model);
                $this->orderPeopleService->updateOrderPeopleEvent(
                    ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'),
                    $post["OrderTrainingWork"]["responsible_id"], $model);
                $model->releaseEvents();
                return $this->redirect(['view', 'id' => $model->id]);
            }
            return $this->render('update', [
                'model' => $model,
                'people' => $people,
                'groups' => $groups,
                'groupParticipant' => $groupParticipant,
                'transferGroups' => $transferGroups,
                'scanFile' => $tables['scan'],
                'docFiles' => $tables['docs'],
                'groupCheckOption' => $groupCheckOption,
                'groupParticipantOption' => $groupParticipantOption,
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentOrderWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }
    public function actionGetListByBranch()
    {
        $branchId = Yii::$app->request->get('branch_id');
        $nomenclatureList = Yii::$app->nomenclature->getListByBranch($branchId); // Получаем список по номеру отдела
        return $this->asJson($nomenclatureList); // Возвращаем список в формате JSON
    }
    public function actionGetGroupByBranch($branch)
    {
        $groupCheckOption = json_decode(Yii::$app->request->get('groupCheckOption'));
        $modelId = Yii::$app->request->get('modelId');
        $groupsQuery = $this->trainingGroupRepository->getByBranchQuery($branch);
        $dataProvider = new ActiveDataProvider([
            'query' => $groupsQuery,
        ]);
        return $this->asJson([
            'gridHtml' => $this->renderPartial(GroupParticipantWidget::GROUP_VIEW, [
                'dataProvider' => $dataProvider,
                'model' => $this->orderTrainingRepository->get($modelId),
                'groupCheckOption' => $groupCheckOption,
            ]),
        ]);
    }
    public function actionGetGroupParticipantsByBranch()
    {
        $groupIds = Yii::$app->request->get('groupIds');
        $modelId = Yii::$app->request->get('modelId');
        $groupIds = json_decode($groupIds);
        if ($modelId == 0){
            $groupCheckOption = [];
            $groupParticipantOption = [];
            $nomenclature = Yii::$app->request->get('nomenclature');
            $status = NomenclatureDictionary::getStatus($nomenclature);
            //create
            if ($status == NomenclatureDictionary::ORDER_ENROLL){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantsToEnrollCreate($groupIds)
                ]);
            }
            if ($status == NomenclatureDictionary::ORDER_DEDUCT){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantsToDeductCreate($groupIds)
                ]);
            }
            if ($status == NomenclatureDictionary::ORDER_TRANSFER){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantsToTransferCreate($groupIds)
                ]);
            }
        }
        else {
            //update


            $model = $this->orderTrainingRepository->get($modelId);
            $status = $this->orderTrainingService->getStatus($model);

            $nomenclature = $model->getNomenclature();
            if ($status == NomenclatureDictionary::ORDER_ENROLL){
                $groupCheckOption = $this->trainingGroupRepository->getAttachedGroupsByOrder($modelId,  $status);
                $groupParticipantOption = $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($modelId, $status);
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToEnrollUpdate($groupIds, $modelId)
                ]);
            }
            else if ($status == NomenclatureDictionary::ORDER_DEDUCT) {
                $groupCheckOption = $this->trainingGroupRepository->getAttachedGroupsByOrder($modelId,  $status);
                $groupParticipantOption = $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($modelId, $status);
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToDeductUpdate($groupIds, $modelId)
                ]);
            }
            else if ($status == NomenclatureDictionary::ORDER_TRANSFER){
                $groupCheckOption = $this->trainingGroupRepository->getAttachedGroupsByOrder($modelId,  $status);
                $groupParticipantOption = $this->trainingGroupParticipantRepository->getAttachedParticipantByOrder($modelId, $status);
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToTransferUpdate($groupIds, $modelId)
                ]);
            }
            else {
                $groupCheckOption = [];
            }
        }
        return $this->asJson([
            'gridHtml' => $this->renderPartial(GroupParticipantWidget::GROUP_PARTICIPANT_VIEW, [
                'dataProvider' => $dataProvider,
                'model' => $this->orderTrainingRepository->get($modelId),
                'nomenclature' => $nomenclature,
                'transferGroups' => $this->trainingGroupRepository->getById($groupIds),
                'groupCheckOption' => $groupCheckOption,
                'groupParticipantOption' => $groupParticipantOption,
            ]),
        ]);
    }
}