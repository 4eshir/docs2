<?php
namespace frontend\controllers\order;
use app\components\DynamicWidget;
use app\models\work\event\ForeignEventWork;
use app\models\work\order\OrderEventWork;
use app\models\work\order\OrderMainWork;
use app\models\work\team\ActParticipantWork;
use app\services\act_participant\ActParticipantService;
use app\services\act_participant\SquadParticipantService;
use app\services\event\OrderEventFormService;
use app\services\order\OrderEventService;
use app\services\order\OrderMainService;
use app\services\team\TeamService;
use common\helpers\files\FilesHelper;
use common\models\scaffold\ActParticipant;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\order\OrderEventRepository;
use common\repositories\order\OrderMainRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\forms\OrderEventForm;
use frontend\helpers\HeaderWizard;
use frontend\models\forms\ActParticipantForm;
use frontend\models\search\SearchOrderEvent;
use frontend\models\search\SearchOrderMain;
use frontend\models\work\general\FilesWork;
use frontend\models\work\general\PeopleWork;
use frontend\services\event\ForeignEventService;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

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
    private SquadParticipantService $squadParticipantService;
    private TeamService $teamService;
    private ActParticipantService $actParticipantService;
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
        TeamService  $teamService,
        ActParticipantService $actParticipantService,
        FileService $fileService,
        FilesRepository $fileRepository,
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
        $this->squadParticipantService = $squadParticipantService;
        $this->foreignEventService = $foreignEventService;
        $this->teamService = $teamService;
        $this->actParticipantService = $actParticipantService;
        $this->fileService = $fileService;
        $this->fileRepository = $fileRepository;
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
        if($model->load($post)) {
            $acts = $post["ActParticipantForm"];
            //$this->actParticipantService->getFilesInstance($modelActParticipant);
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
                2 , //$model->type,
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
            'modelActs' => $modelActs
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
        $model = OrderEventForm::fill($modelOrderEvent, $foreignEvent);
        $tables = $this->orderMainService->getUploadedFilesTables($modelOrderEvent);
        $modelResponsiblePeople = $this->orderMainService->getResponsiblePeopleTable($modelOrderEvent->id);
        $foreignEventTable = $this->foreignEventService->getForeignEventTable($foreignEvent);
        $awardTable = $this->foreignEventService->getAwardTable($foreignEvent);
        $teamTable = $this->teamService->getTeamTable($foreignEvent);
        $respPeopleId = DynamicWidget::getData(basename(OrderEventForm::class), "responsible_id", $post);
        if($model->load($post)){
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
            return $this->redirect(['view', 'id' => $modelOrderEvent->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'people' => $people,
            'modelResponsiblePeople' => $modelResponsiblePeople,
            'scanFile' => $tables['scan'],
            'docFiles' => $tables['docs'],
            'foreignEventTable' => $foreignEventTable,
            'teamTable' => $teamTable,
            'awardTable' => $awardTable
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