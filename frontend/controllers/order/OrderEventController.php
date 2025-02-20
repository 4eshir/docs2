<?php

namespace frontend\controllers\order;

use app\components\DynamicWidget;
use app\models\work\order\OrderEventGenerateWork;
use app\services\order\OrderEventGenerateService;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\traits\AccessControl;
use common\models\scaffold\OrderEventGenerate;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\order\OrderEventGenerateRepository;
use frontend\models\work\event\ForeignEventWork;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderEventWork;
use frontend\models\work\team\ActParticipantWork;
use frontend\services\act_participant\ActParticipantService;
use frontend\services\event\OrderEventFormService;
use frontend\services\order\DocumentOrderService;

use frontend\services\order\OrderPeopleService;
use frontend\services\team\TeamService;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\CompanyRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\order\OrderEventRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\facades\ActParticipantFacade;
use frontend\facades\OrderEventFacade;
use frontend\forms\OrderEventForm;
use frontend\models\forms\ActParticipantForm;
use frontend\models\search\SearchOrderEvent;
use frontend\services\event\ForeignEventService;
use Yii;
use yii\helpers\ArrayHelper;
class OrderEventController extends DocumentController
{
    use AccessControl;

    private OrderPeopleService $orderPeopleService;
    private DocumentOrderService $documentOrderService;
    private PeopleRepository $peopleRepository;
    private OrderEventRepository $orderEventRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private ForeignEventRepository $foreignEventRepository;
    private OrderEventFormService $orderEventFormService;
    private ForeignEventService $foreignEventService;
    private ActParticipantService $actParticipantService;
    private ActParticipantRepository $actParticipantRepository;
    private ActParticipantFacade $actParticipantFacade;
    private OrderEventFacade $orderEventFacade;
    private ForeignEventParticipantsRepository $foreignEventParticipantsRepository;
    private CompanyRepository $companyRepository;
    private LockWizard $lockWizard;
    private OrderEventGenerateRepository $orderEventGenerateRepository;
    private OrderEventGenerateService $orderEventGenerateService;

    public function __construct(
        $id, $module,
        OrderPeopleService $orderPeopleService,
        DocumentOrderService $documentOrderService,
        PeopleRepository $peopleRepository,
        OrderEventRepository $orderEventRepository,
        OrderPeopleRepository $orderPeopleRepository,
        ForeignEventRepository $foreignEventRepository,
        OrderEventFormService $orderEventFormService,
        ForeignEventService $foreignEventService,
        ActParticipantService $actParticipantService,
        ActParticipantRepository $actParticipantRepository,
        FileService $fileService,
        FilesRepository $fileRepository,
        ActParticipantFacade $actParticipantFacade,
        OrderEventFacade $orderEventFacade,
        TeamService $teamService,
        ForeignEventParticipantsRepository $foreignEventParticipantsRepository,
        CompanyRepository $companyRepository,
        LockWizard $lockWizard,
        OrderEventGenerateRepository $orderEventGenerateRepository,
        OrderEventGenerateService $orderEventGenerateService,
        $config = []
    )
    {
        $this->orderPeopleService = $orderPeopleService;
        $this->documentOrderService = $documentOrderService;
        $this->peopleRepository = $peopleRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->orderEventRepository = $orderEventRepository;
        $this->orderEventFormService = $orderEventFormService;
        $this->foreignEventService = $foreignEventService;
        $this->actParticipantService = $actParticipantService;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->actParticipantFacade = $actParticipantFacade;
        $this->orderEventFacade = $orderEventFacade;
        $this->foreignEventParticipantsRepository = $foreignEventParticipantsRepository;
        $this->companyRepository = $companyRepository;
        $this->lockWizard = $lockWizard;
        $this->orderEventGenerateRepository = $orderEventGenerateRepository;
        $this->orderEventGenerateService = $orderEventGenerateService;
        parent::__construct($id, $module, $fileService, $fileRepository, $config);
    }
    public function actionIndex() {
        $searchModel = new SearchOrderEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCreate() {
        /* @var OrderEventForm $model */
        $model = new OrderEventForm();
        $people = $this->peopleRepository->getOrderedList();
        $modelActs = [new ActParticipantForm];
        $post = Yii::$app->request->post();
        $teams = [];
        $nominations = [];
        $participants = $this->foreignEventParticipantsRepository->getSortedList();
        $company = $this->companyRepository->getList();
        if($model->load($post)) {
            $acts = $post["ActParticipantForm"];
            if (!$model->validate()) {
                  throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->orderEventFormService->getFilesInstances($model);
            $respPeopleId = DynamicWidget::getData(basename(OrderEventForm::class), "responsible_id", $post);
            $modelOrderEvent = OrderEventWork::fill(
                $model->order_copy_id,
                NomenclatureDictionary::ADMIN_ORDER,
                $model->order_postfix,
                $model->order_date,
                $model->order_name,
                $model->signed_id,
                $model->bring_id,
                $model->executor_id,
                $model->key_words,
                $model->creator_id,
                $model->last_edit_id,
                $model->target,
                DocumentOrderWork::ORDER_EVENT, //$model->type,
                $model->state,
                $model->nomenclature_id,
                $model->study_type,
                $model->scanFile,
                $model->docFiles,
            );
            $modelOrderEvent->generateOrderNumber();
            $this->documentOrderService->getPeopleStamps($modelOrderEvent);
            $number = $modelOrderEvent->getNumberPostfix();
            $this->orderEventRepository->save($modelOrderEvent);
            $generateInfo = OrderEventGenerateWork::fill(
                $modelOrderEvent->id,
                $model->purpose,
                $model->docEvent,
                $model->respPeopleInfo,
                $model->timeProvisionDay,
                $model->extraRespInsert,
                $model->timeInsertDay,
                $model->extraRespMethod,
                $model->extraRespInfoStuff
            );
            $this->orderEventGenerateService->setPeopleStamp($generateInfo);
            $this->orderEventGenerateRepository->save($generateInfo);
            $this->documentOrderService->saveFilesFromModel($modelOrderEvent);
            $modelForeignEvent = ForeignEventWork::fill(
                $model->eventName,
                $model->organizer_id,
                $model->dateBegin,
                $model->dateEnd,
                $model->city,
                $model->eventWay,
                $model->eventLevel,
                $model->minister,
                $model->minAge,
                $model->maxAge,
                $model->keyEventWords,
                $modelOrderEvent->id,
                $model->actFiles
            );
            $this->foreignEventRepository->save($modelForeignEvent);
            $this->orderPeopleService->addOrderPeopleEvent($respPeopleId, $modelOrderEvent);
            $this->foreignEventService->saveActFilesFromModel($modelForeignEvent, $model->actFiles, $number);
            $model->releaseEvents();
            $modelForeignEvent->releaseEvents();
            $modelOrderEvent->releaseEvents();
            $this->actParticipantService->addActParticipant($acts, $modelForeignEvent->id);
            return $this->redirect(['view', 'id' => $modelOrderEvent->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people,
            'modelActs' => $modelActs,
            'nominations' => $nominations,
            'teams' => $teams,
            'participants' => $participants,
            'company' => $company
        ]);
    }
    public function actionView($id)
    {
        /* @var OrderEventWork $modelOrderEvent */
        /* @var ForeignEventWork $foreignEvent */
        $modelResponsiblePeople = implode('<br>',
            $this->documentOrderService->createOrderPeopleArray(
                $this->orderPeopleRepository->getResponsiblePeople($id)
            )
        );
        $modelOrderEvent = $this->orderEventRepository->get($id);
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
        $actTable = $this->actParticipantService->createActTable($foreignEvent->id);
        return $this->render('view',
            [
                'model' => $modelOrderEvent,
                'foreignEvent' => $foreignEvent,
                'modelResponsiblePeople' => $modelResponsiblePeople,
                'actTable' => $actTable
            ]
        );
    }
    public function actionUpdate($id)
    {
        /* @var $modelOrderEvent OrderEventWork */
        if ($this->lockWizard->lockObject($id, DocumentOrderWork::tableName(), Yii::$app->user->id)) {
            $data = $this->orderEventFacade->prepareOrderEventUpdateFacade($id);
            $people = $data['people'];
            $tables = $data['tables'];
            $nominations = $data['nominations'];
            $teams = $data['teams'];
            $modelActForms = $data['modelActForms'];
            $actTable = $data['actTable'];
            $model = $data['model'];
            $modelForeignEvent = $data['modelForeignEvent'];
            $modelOrderEvent = $data['modelOrderEvent'];
            $modelData = $this->orderEventFacade->modelOrderEventFormFacade($model, $id);
            $orderNumber = $modelData['orderNumber'];
            $participants = $this->foreignEventParticipantsRepository->getSortedList();
            $company = $this->companyRepository->getList();
            $model->setValuesForUpdate();
            $post = Yii::$app->request->post();
            if ($model->load($post)) {
                $this->lockWizard->unlockObject($id, DocumentOrderWork::tableName());
                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }
                $acts = $post["ActParticipantForm"];
                $this->orderEventFormService->getFilesInstances($model);
                $modelOrderEvent->fillUpdate(
                    $model->order_copy_id,
                    $orderNumber,
                    $model->order_postfix,
                    $model->order_date,
                    $model->order_name,
                    $model->signed_id,
                    $model->bring_id,
                    $model->executor_id,
                    $model->key_words,
                    $model->creator_id,
                    $model->last_edit_id,
                    $model->target,
                    DocumentOrderWork::ORDER_EVENT, //$model->type,
                    $model->state,
                    $model->nomenclature_id,
                    $model->study_type,
                    $model->scanFile,
                    $model->docFiles,
                );
                $this->documentOrderService->getPeopleStamps($modelOrderEvent);
                $this->orderEventRepository->save($modelOrderEvent);
                $generateInfo = $this->orderEventGenerateRepository->getByOrderId($id);
                $generateInfo->fillUpdate(
                    $modelOrderEvent->id,
                    $model->purpose,
                    $model->docEvent,
                    $model->respPeopleInfo,
                    $model->timeProvisionDay,
                    $model->extraRespInsert,
                    $model->timeInsertDay,
                    $model->extraRespMethod,
                    $model->extraRespInfoStuff
                );
                $this->orderEventGenerateService->setPeopleStamp($generateInfo);
                $this->orderEventGenerateRepository->save($generateInfo);
                $this->documentOrderService->saveFilesFromModel($modelOrderEvent);
                $this->orderPeopleService->updateOrderPeopleEvent(
                    ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'),
                    $post["OrderEventForm"]["responsible_id"], $modelOrderEvent);
                $modelForeignEvent->fillUpdate(
                    $model->eventName,
                    $model->organizer_id,
                    $model->dateBegin,
                    $model->dateEnd,
                    $model->city,
                    $model->eventWay,
                    $model->eventLevel,
                    $model->minister,
                    $model->minAge,
                    $model->maxAge,
                    $model->keyEventWords,
                    $modelOrderEvent->id,
                    $model->actFiles
                );
                $this->foreignEventRepository->save($modelForeignEvent);
                $this->actParticipantService->addActParticipant($acts, $modelForeignEvent->id);
                $modelOrderEvent->releaseEvents();
                return $this->redirect(['view', 'id' => $modelOrderEvent->id]);
            }
            return $this->render('update', [
                'model' => $model,
                'people' => $people,
                'scanFile' => $tables['scan'],
                'docFiles' => $tables['docs'],
                'nominations' => $nominations,
                'teams' => $teams,
                'modelActs' => $modelActForms,
                'actTable' => $actTable,
                'participants' => $participants,
                'company' => $company,
                'id' => $id
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentOrderWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }
    public function actionAct($id)
    {
        /* @var $act ActParticipantWork */
        $act = [$this->actParticipantRepository->get($id)];
        $foreignEventId = $act[0]->foreign_event_id;
        $orderId = ($this->foreignEventRepository->get($foreignEventId))->order_participant_id;
        if($act[0] == NULL){
            return $this->redirect(['index']);
        }
        $this->actParticipantService->getPeopleStamp($act[0]);
        $data = $this->actParticipantFacade->prepareActFacade($act);
        $modelAct = $data['modelAct'];
        $people = $data['people'];
        $nominations = $data['nominations'];
        $teams = $data['teams'];
        $defaultTeam = $data['defaultTeam'];
        $tables = $data['tables'];
        $participants = $data['participants'];
        $post = Yii::$app->request->post();
        if($post != NULL){
            $post = $post["ActParticipantForm"];
            $act[0]->fillUpdate(
                $post[0]["firstTeacher"],
                $post[0]["secondTeacher"],
                $act[0]->team_name_id,
                $act[0]->foreign_event_id,
                $act[0]->focus,
                $act[0]->type,
                NULL,
                $act[0]->nomination,
                $act[0]->form
            );
            $this->actParticipantService->setPeopleStamp($act[0]);
            $this->actParticipantRepository->save($act[0]);
            $this->actParticipantService->getFilesInstance($modelAct[0], 0);
            $act[0]->actFiles = $modelAct[0]->actFiles;
            $this->actParticipantService->saveFilesFromModel($act[0], 0);
            //при замене select в act-update заменить в следующей строчке $post[0]["participant"] на что-то другое
            $this->actParticipantService->updateSquadParticipant($act[0], $post[0]["participant"]);
            return $this->redirect(['view', 'id' => $orderId]);
        }
        return $this->render('act-update', [
            'act' => $act[0],
            'modelActs' => $modelAct,
            'people' => $people,
            'nominations' => $nominations,
            'teams' => $teams,
            'defaultTeam' => $defaultTeam['name'],
            'tables' => $tables,
            'participants' => $participants,
            'orderId' => $orderId,
        ]);
    }
    public function actionDeletePeople($id, $modelId)
    {
        $this->orderPeopleRepository->deleteByPeopleId($id);
        return $this->redirect(['update', 'id' => $modelId]);
    }
    public function actionActDelete($id)
    {
        $model = $this->actParticipantRepository->get($id);
        $foreignEvent = $this->foreignEventRepository->get($model->foreign_event_id);
        $order = $this->orderEventRepository->get($foreignEvent->order_participant_id);
        $this->actParticipantRepository->delete($model);
        return $this->redirect(['update', 'id' => $order->id]);
    }

    public function beforeAction($action)
    {
        $result = $this->checkActionAccess($action);
        if ($result['url'] !== '') {
            $this->redirect($result['url']);
            return $result['status'];
        }

        return parent::beforeAction($action);
    }
}