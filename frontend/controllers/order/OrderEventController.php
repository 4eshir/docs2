<?php
namespace frontend\controllers\order;
use app\components\DynamicWidget;
use app\models\work\event\ForeignEventWork;
use app\models\work\order\OrderEventWork;
use app\models\work\order\OrderMainWork;
use app\services\event\OrderEventFormService;
use app\services\order\OrderEventService;
use app\services\order\OrderMainService;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\order\OrderEventRepository;
use common\repositories\order\OrderMainRepository;
use DomainException;
use frontend\forms\OrderEventForm;
use Yii;
use yii\web\Controller;
class OrderEventController extends Controller
{
    private PeopleRepository $peopleRepository;
    private OrderEventRepository $orderEventRepository;
    private ForeignEventRepository $foreignEventRepository;
    private OrderMainService $orderMainService;
    private OrderEventFormService $orderEventFormService;
    private OrderEventService  $orderEventService;
    public function __construct(
        $id, $module,
        PeopleRepository $peopleRepository,
        OrderEventRepository $orderEventRepository,
        ForeignEventRepository $foreignEventRepository,
        OrderMainService $orderMainService,
        OrderEventFormService $orderEventFormService,
        OrderEventService $orderEventService,
        $config = []
    )
    {
        $this->peopleRepository = $peopleRepository;
        $this->orderMainService = $orderMainService;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->orderEventRepository = $orderEventRepository;
        $this->orderEventService = $orderEventService;
        $this->orderEventFormService = $orderEventFormService;
        parent::__construct($id, $module, $config);
    }
    public function actionIndex() {
    }
    public function actionCreate() {
        /* @var OrderEventForm $model */
        $model = new OrderEventForm();
        $people = $this->peopleRepository->getOrderedList();
        $post = Yii::$app->request->post();

        if($model->load($post)) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->orderEventFormService->getFilesInstances($model);
            $respPeopleId = DynamicWidget::getData(basename(OrderEventForm::class), "responsible_id", $post);
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
                    $model->keyEventWords
            );
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
                    $model->type,
                    $model->state,
                    $model->nomenclature_id,
                    $model->study_type,
                    $model->scanFile,
                    $model->docFiles
            );
            $modelOrderEvent->generateOrderNumber();
            $this->foreignEventRepository->save($modelForeignEvent);
            $this->orderEventRepository->save($modelOrderEvent);
            $this->orderEventService->saveFilesFromModel($modelOrderEvent);
            $this->orderMainService->addOrderPeopleEvent($respPeopleId, $modelOrderEvent);
            $modelOrderEvent->releaseEvents();
            return $this->redirect('view', [
                'id' => $modelOrderEvent->id
            ]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people
        ]);
    }
    public function actionView($id)
    {
        return $this->render('view'
        );
    }

    public function actionUpdate($id) {

    }
}