<?php

namespace frontend\controllers\order;

use app\components\DynamicWidget;
use app\models\search\SearchOrderTraining;
use app\models\work\order\OrderTrainingWork;
use app\services\order\OrderMainService;
use app\services\order\OrderTrainingService;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\controllers\DocumentController;
use common\models\scaffold\TrainingGroupParticipant;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\order\OrderTrainingRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\services\educational\OrderTrainingGroupParticipantService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class OrderTrainingController extends DocumentController
{
    private PeopleRepository $peopleRepository;
    private OrderMainService $orderMainService;
    private OrderTrainingService $orderTrainingService;
    private OrderPeopleRepository $orderPeopleRepository;
    private OrderTrainingRepository $orderTrainingRepository;
    private OrderTrainingGroupParticipantService $orderTrainingGroupParticipantService;
    private TrainingGroupRepository $trainingGroupRepository;
    private TrainingGroupParticipantRepository $trainingGroupParticipantRepository;

    public function __construct(
        $id,
        $module,
        PeopleRepository $peopleRepository,
        OrderMainService $orderMainService,
        OrderTrainingService $orderTrainingService,
        OrderPeopleRepository $orderPeopleRepository,
        OrderTrainingRepository $orderTrainingRepository,
        OrderTrainingGroupParticipantService $orderTrainingGroupParticipantService,
        TrainingGroupRepository $trainingGroupRepository,
        TrainingGroupParticipantRepository $trainingGroupParticipantRepository,
        FileService $fileService,
        FilesRepository $filesRepository,
        $config = []
    )
    {
        $this->peopleRepository = $peopleRepository;
        $this->orderMainService = $orderMainService;
        $this->orderTrainingService = $orderTrainingService;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->orderTrainingRepository = $orderTrainingRepository;
        $this->orderTrainingGroupParticipantService = $orderTrainingGroupParticipantService;
        $this->trainingGroupRepository = $trainingGroupRepository;
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
            $this->orderTrainingService->createOrderPeopleArray(
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
        $people = $this->peopleRepository->getOrderedList();
        $post = Yii::$app->request->post();
        $groups = $this->orderTrainingRepository->getEmptyOrderTrainingGroupList();
        $groupParticipant = $this->orderTrainingRepository->getEmptyOrderTrainingGroupParticipantData();
        if ($model->load($post)) {
            if (!$model->validate()) {
               throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $respPeopleId = DynamicWidget::getData(basename(OrderTrainingWork::class), "responsible_id", $post);
            $this->orderTrainingService->getFilesInstances($model);
            $model->generateOrderNumber();
            $this->orderTrainingRepository->save($model);
            $participants = $post['group-participant-selection'];
            $status = $this->orderTrainingService->getStatus($model);
            $this->orderTrainingGroupParticipantService->addOrderTrainingGroupParticipantEvent($model, $model->id, $participants, $status);
            $this->orderTrainingService->updateTrainingGroupParticipantStatus($participants, $status);
            $this->orderTrainingService->saveFilesFromModel($model);
            $this->orderMainService->addOrderPeopleEvent($respPeopleId, $model);
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people,
            'groups' => $groups,
            'groupParticipant' => $groupParticipant
        ]);
    }
    public function actionUpdate($id)
    {
        $model = $this->orderTrainingRepository->get($id);
        $this->orderTrainingService->setBranch($model);
        $people = $this->peopleRepository->getOrderedList();
        $model->responsible_id = ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id');
        $post = Yii::$app->request->post();
        $number = $model->order_number;
        $groups = $this->orderTrainingRepository->getOrderTrainingGroupData($model);
        $groupParticipant = $this->orderTrainingRepository->getOrderTrainingGroupParticipantData($model);
        if ($model->load($post) && $model->validate()) {
            $this->orderTrainingService->getFilesInstances($model);
            $model->order_number = $number;
            $this->orderTrainingRepository->save($model);
            $this->orderTrainingService->saveFilesFromModel($model);
            $this->orderTrainingService->updateOrderPeopleEvent(
                ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'),
                $post["OrderTrainingWork"]["responsible_id"], $model);
            $participants = $post['group-participant-selection'];
            $status = $this->orderTrainingService->getStatus($model);
            $this->orderTrainingGroupParticipantService->updateOrderTrainingGroupParticipant($model, $model->id, $participants, $status);
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'people' => $people,
            'groups' => $groups,
            'groupParticipant' => $groupParticipant
        ]);
    }
    public function actionGetListByBranch()
    {
        $branchId = Yii::$app->request->get('branch_id');
        $nomenclatureList = Yii::$app->nomenclature->getListByBranch($branchId); // Получаем список по номеру отдела
        return $this->asJson($nomenclatureList); // Возвращаем список в формате JSON
    }
    public function actionGetGroupByBranch($branch)
    {
        $modelId = Yii::$app->request->get('modelId');
        $groupsQuery = $this->trainingGroupRepository->getByBranchQuery($branch);
        $dataProvider = new ActiveDataProvider([
            'query' => $groupsQuery,
        ]);
        return $this->asJson([
            'gridHtml' => $this->renderPartial('_groups_grid', [
                'dataProvider' => $dataProvider,
                'model' => $this->orderTrainingRepository->get($modelId)
            ]),
        ]);
    }
    public function actionGetGroupParticipantsByBranch($groupIds)
    {
        $modelId = Yii::$app->request->get('modelId');
        $nomenclature = Yii::$app->request->get('nomenclature');
        $groupIds = json_decode($groupIds);
        if ($modelId == 0){
            $status = NomenclatureDictionary::getStatus($nomenclature);
            //create
            if($status == 1){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getAllParticipantToEnrollByGroupQuery($groupIds)
                ]);
            }
            if($status == 2){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getAllParticipantToDeductByGroupQuery($groupIds)
                ]);
            }
        } else {
            //update
            $model = $this->orderTrainingRepository->get($modelId);
            $status = $this->orderTrainingService->getStatus($model);
            if ($status == 1){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToEnrollUpdate($groupIds, $modelId)
                ]);
            }
            if ($status == 2){
                $dataProvider = new ActiveDataProvider([
                    'query' => $this->trainingGroupParticipantRepository->getParticipantToDeductUpdate($groupIds, $modelId)
                ]);
            }
        }
        return $this->asJson([
            'gridHtml' => $this->renderPartial('_group-participant_grid', [
                'dataProvider' => $dataProvider,
                'model' => $this->orderTrainingRepository->get($modelId)
            ]),
        ]);
    }
}