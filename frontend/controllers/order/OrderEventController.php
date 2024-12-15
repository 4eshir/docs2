<?php
namespace frontend\controllers\order;
use app\components\DynamicWidget;
use app\models\work\event\ForeignEventWork;
use app\models\work\order\OrderEventWork;
use app\models\work\team\ActParticipantWork;
use app\services\act_participant\ActParticipantService;
use app\services\act_participant\SquadParticipantService;
use app\services\event\OrderEventFormService;
use app\services\order\OrderEventService;
use app\services\order\OrderMainService;
use app\services\team\TeamService;
use common\helpers\files\FilesHelper;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\order\OrderEventRepository;
use common\repositories\team\TeamRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\forms\OrderEventForm;
use frontend\helpers\HeaderWizard;
use frontend\models\forms\ActParticipantForm;
use frontend\models\search\SearchOrderEvent;
use frontend\models\work\general\FilesWork;
use frontend\services\event\ForeignEventService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
class OrderEventController extends Controller
{
    private PeopleRepository $peopleRepository;
    private FileService $fileService;
    private FilesRepository $fileRepository;
    private OrderEventRepository $orderEventRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private ForeignEventRepository $foreignEventRepository;
    private OrderMainService $orderMainService;
    private OrderEventFormService $orderEventFormService;
    private ForeignEventService $foreignEventService;
    private OrderEventService $orderEventService;
    private ActParticipantService $actParticipantService;
    public ActParticipantRepository $actParticipantRepository;
    private TeamRepository $teamRepository;
    private TeamService $teamService;
    public function __construct(
        $id, $module,
        PeopleRepository $peopleRepository,
        OrderEventRepository $orderEventRepository,
        OrderPeopleRepository $orderPeopleRepository,
        ForeignEventRepository $foreignEventRepository,
        OrderMainService $orderMainService,
        OrderEventFormService $orderEventFormService,
        ForeignEventService $foreignEventService,
        OrderEventService $orderEventService,
        SquadParticipantService $squadParticipantService,
        ActParticipantService $actParticipantService,
        ActParticipantRepository $actParticipantRepository,
        FileService $fileService,
        FilesRepository $fileRepository,
        TeamRepository $teamRepository,
        TeamService $teamService,
        $config = []
    )
    {
        $this->peopleRepository = $peopleRepository;
        $this->orderMainService = $orderMainService;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->orderEventRepository = $orderEventRepository;
        $this->orderEventService = $orderEventService;
        $this->orderEventFormService = $orderEventFormService;
        $this->foreignEventService = $foreignEventService;
        $this->actParticipantService = $actParticipantService;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->fileService = $fileService;
        $this->fileRepository = $fileRepository;
        $this->teamRepository = $teamRepository;
        $this->teamService = $teamService;
        parent::__construct($id, $module, $config);
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
        if($model->load($post)) {
            $acts = $post["ActParticipantForm"];
            if (!$model->validate()) {
                  throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->orderEventFormService->getFilesInstances($model);
            $respPeopleId = DynamicWidget::getData(basename(OrderEventForm::class), "responsible_id", $post);
            $modelOrderEvent = OrderEventWork::fill(
                $model->order_copy_id,
                $model->order_number,
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
                2, //$model->type,
                $model->state,
                $model->nomenclature_id,
                $model->study_type,
                $model->scanFile,
                $model->docFiles,
            );
            $modelOrderEvent->generateOrderNumber();
            $number = $modelOrderEvent->getNumberPostfix();
            $this->orderEventRepository->save($modelOrderEvent);
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
            $this->orderEventService->saveFilesFromModel($modelOrderEvent);
            $this->orderMainService->addOrderPeopleEvent($respPeopleId, $modelOrderEvent);
            $this->foreignEventService->saveFilesFromModel($modelForeignEvent, $model->actFiles, $number);
            $model->releaseEvents();
            $modelForeignEvent->releaseEvents();
            $modelOrderEvent->releaseEvents();
            $this->actParticipantService->addActParticipantEvent($acts, $modelForeignEvent->id);
            var_dump('OK!');
            return $this->redirect(['view', 'id' => $modelOrderEvent->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people,
            'modelActs' => $modelActs,
            'nominations' => $nominations,
            'teams' => $teams,
        ]);
    }
    public function actionView($id)
    {
        /* @var OrderEventWork $modelOrderEvent */
        /* @var ForeignEventWork $foreignEvent */
        $modelResponsiblePeople = implode('<br>',
            $this->orderMainService->createOrderPeopleArray(
                $this->orderPeopleRepository->getResponsiblePeople($id)
            )
        );
        $modelOrderEvent = $this->orderEventRepository->get($id);
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
        return $this->render('view',
            [
                'model' => $modelOrderEvent,
                'foreignEvent' => $foreignEvent,
                'modelResponsiblePeople' => $modelResponsiblePeople
            ]
        );
    }
    public function actionUpdate($id) {
        /* @var OrderEventWork $modelOrderEvent */
        /* @var ForeignEventWork $foreignEvent */
        /* @var OrderEventForm $model */
        $modelOrderEvent = $this->orderEventRepository->get($id);
        $people = $this->peopleRepository->getOrderedList();
        $post = Yii::$app->request->post();
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($modelOrderEvent->id);
        $modelActs = $this->actParticipantRepository->getByForeignEventId($foreignEvent->id);
        //$modelActForms = $this->actParticipantService->createForms($modelActs);
        $modelActForms = [new ActParticipantForm];
        $model = OrderEventForm::fill($modelOrderEvent, $foreignEvent);
        $tables = $this->orderMainService->getUploadedFilesTables($modelOrderEvent);
        $modelResponsiblePeople = $this->orderMainService->getResponsiblePeopleTable($modelOrderEvent->id);
        $actTable = $this->actParticipantService->createActTable($foreignEvent->id);
        $nominations = ArrayHelper::getColumn($this->actParticipantRepository->getByForeignEventId($foreignEvent->id), 'nomination'); //номинации
        $teams = $this->teamService->getNamesByForeignEventId($foreignEvent->id);
        if($model->load($post)){
            /*$respPeopleId = DynamicWidget::getData(basename(OrderEventForm::class), "responsible_id", $post);
            $modelOrderEvent->fillUpdate(
                $model->order_copy_id,
                $model->order_number,
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
                2 , //$model->type,
                $model->state,
                $model->nomenclature_id,
                $model->study_type,
                $model->scanFile,
                $model->docFiles,
            );
            $this->orderEventRepository->save($modelOrderEvent);
            $foreignEvent->fillUpdate(
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
            $foreignEvent->save();
            return $this->redirect(['view', 'id' => $modelOrderEvent->id]);*/
        }
        return $this->render('update', [
            'model' => $model,
            'people' => $people,
            'modelResponsiblePeople' => $modelResponsiblePeople,
            'scanFile' => $tables['scan'],
            'docFiles' => $tables['docs'],
            'nominations' => $nominations,
            'teams' => $teams,
            'modelActs' => $modelActForms,
            'actTable' => $actTable,
        ]);
    }
    public function actionGetFile($filepath)
    {
        $data = $this->fileService->downloadFile($filepath);
        if ($data['type'] == FilesHelper::FILE_SERVER) {
            Yii::$app->response->sendFile($data['obj']->file);
        }
        else {
            $fp = fopen('php://output', 'r');
            HeaderWizard::setFileHeaders(FilesHelper::getFilenameFromPath($data['obj']->filepath), $data['obj']->file->size);
            $data['obj']->file->download($fp);
            fseek($fp, 0);
        }
    }
    public function actionDeleteFile($modelId, $fileId)
    {
        try {
            $file = $this->fileRepository->getById($fileId);
            /** @var FilesWork $file */
            $filepath = $file ? basename($file->filepath) : '';
            $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
            $file->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            $file->releaseEvents();
            Yii::$app->session->setFlash('success', "Файл $filepath успешно удален");
            return $this->redirect(['update', 'id' => $modelId]);
        }
        catch (DomainException $e) {
            return 'Oops! Something wrong';
        }
    }
    public function actionAct($id)
    {
        /* @var $act ActParticipantWork */
        $act = [$this->actParticipantRepository->getById($id)];
        $modelAct = $this->actParticipantService->createForms($act);
        $people = $this->peopleRepository->getOrderedList();
        $nominations = ArrayHelper::getColumn($this->actParticipantRepository->getByForeignEventId($act[0]->foreign_event_id), 'nomination'); //номинации
        $teams = $this->teamService->getNamesByForeignEventId($act[0]->foreign_event_id);
        $defaultTeam = $this->teamRepository->getById($act[0]->team_name_id);
        $post = Yii::$app->request->post();
        if($post != NULL){
            $post = $post["ActParticipantForm"];
            $foreignEventId = $act[0]->foreign_event_id;
            $team = $this->teamRepository->getByNameAndForeignEventId($foreignEventId, $post[0]["team"]);
            $act[0]->fillUpdate(
                $post[0]["firstTeacher"],
                $post[0]["secondTeacher"],
                $team->id,
                $foreignEventId,
                $post[0]["branch"],
                $post[0]["focus"],
                $post[0]["type"],
                NULL,
                $post[0]["nomination"],
                $post[0]["form"],
            );
            $act[0]->save();
        }
        return $this->render('act-update', [
            'act' => $act[0],
            'modelActs' => $modelAct,
            'people' => $people,
            'nominations' => $nominations,
            'teams' => $teams,
            'defaultTeam' => $defaultTeam['name'],
        ]);
    }
    public function actionActUpdate($model, $id) {
        $act = $this->actParticipantRepository->getById($id);
        $foreignEvent = $this->foreignEventRepository->get($act->id);
        $post = Yii::$app->request->post();
        return $this->redirect(['update', 'id' => $foreignEvent->order_participant_id]);
    }
    public function actionDeletePeople($id, $modelId)
    {
        $this->orderPeopleRepository->deleteByPeopleId($id);
        return $this->redirect(['update', 'id' => $modelId]);
    }
    public function actionDeleteActParticipant($id)
    {
        return $this->redirect(['update', 'id' => $id]);
    }
    public function actionDeleteTeam($id)
    {
        return $this->redirect(['update', 'id' => $id]);
    }
    public function actionDeleteAward($id)
    {
        return $this->redirect(['update', 'id' => $id]);
    }
}