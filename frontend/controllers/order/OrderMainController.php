<?php
namespace frontend\controllers\order;
use app\models\work\order\OrderMainWork;
use app\services\order\DocumentOrderService;
use app\services\order\OrderMainService;
use app\services\order\OrderPeopleService;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentOrder;
use common\repositories\expire\ExpireRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\general\UserRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use common\repositories\order\OrderMainRepository;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\helpers\HeaderWizard;
use frontend\models\forms\ExpireForm;
use frontend\models\search\SearchOrderMain;
use frontend\models\work\general\FilesWork;
use yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class OrderMainController extends DocumentController
{
    private OrderMainRepository $repository;
    private DocumentOrderRepository $documentOrderRepository;
    private OrderMainService $service;
    public DocumentOrderService $documentOrderService;
    private ExpireRepository $expireRepository;
    private PeopleStampRepository $peopleStampRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private UserRepository $userRepository;
    private RegulationRepository $regulationRepository;
    private LockWizard $lockWizard;
    private OrderPeopleService $orderPeopleService;

    public function __construct(
        $id,
        $module,
        OrderMainRepository $repository,
        DocumentOrderRepository $documentOrderRepository,
        OrderMainService $service,
        DocumentOrderService $documentOrderService,
        ExpireRepository $expireRepository,
        PeopleStampRepository $peopleStampRepository,
        OrderPeopleRepository $orderPeopleRepository,
        UserRepository $userRepository,
        RegulationRepository $regulationRepository,
        LockWizard $lockWizard,
        OrderPeopleService $orderPeopleService,
        $config = []
    )
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->service = $service;
        $this->documentOrderService = $documentOrderService;
        $this->documentOrderRepository = $documentOrderRepository;
        $this->expireRepository = $expireRepository;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->userRepository = $userRepository;
        $this->regulationRepository = $regulationRepository;
        $this->lockWizard = $lockWizard;
        $this->repository = $repository;
        $this->orderPeopleService = $orderPeopleService;

    }
    public function actionIndex(){
        $searchModel = new SearchOrderMain();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionCreate(){
        $model = new OrderMainWork();
        $people = $this->peopleStampRepository->getAll();
        $users = $this->userRepository->getAll();
        $orders = $this->documentOrderRepository->getAll();
        $regulations = $this->regulationRepository->getOrderedList();
        $modelExpire = [new ExpireForm()];
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $model->generateOrderNumber();
            $this->repository->save($model);
            $this->documentOrderService->getFilesInstances($model);
            $this->service->addExpireEvent($post["ExpireForm"], $model);
            $this->orderPeopleService->addOrderPeopleEvent($post["OrderMainWork"]["responsiblePeople"], $model);
            $this->documentOrderService->saveFilesFromModel($model);
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people,
            'users' => $users,
            'modelExpire' => $modelExpire,
            'orders' => $orders,
            'regulations' => $regulations
        ]);
    }
    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, DocumentOrder::tableName(), Yii::$app->user->id)) {
            /* @var OrderMainWork $model */
            $model = $this->repository->get($id);
            $people = $this->peopleStampRepository->getAll();
            $post = Yii::$app->request->post();
            $orders = $this->documentOrderRepository->getAll();
            $regulations = $this->regulationRepository->getOrderedList();
            $users = $this->userRepository->getAll();
            $modelExpire = [new ExpireForm()];
            $modelChangedDocuments = $this->service->getChangedDocumentsTable($model->id);
            $tables = $this->documentOrderService->getUploadedFilesTables($model);
            $model->setResponsiblePeople(ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'));
            if ($model->load($post)) {
                $this->lockWizard->unlockObject($id, DocumentOrder::tableName());
                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }
                $this->repository->save($model);
                $this->documentOrderService->getFilesInstances($model);
                $this->orderPeopleService->updateOrderPeopleEvent(ArrayHelper::getColumn($this->orderPeopleRepository->getResponsiblePeople($id), 'people_id'),
                    $post["OrderMainWork"]["responsiblePeople"], $model);
                $this->service->addExpireEvent($post["ExpireForm"], $model);
                $this->documentOrderService->saveFilesFromModel($model);
                $model->releaseEvents();
                return $this->redirect(['view', 'id' => $model->id]);
            }
            return $this->render('update', [
                'orders' => $orders,
                'model' => $model,
                'people' => $people,
                'users' => $users,
                'modelExpire' => $modelExpire,
                'regulations' => $regulations,
                'modelChangedDocuments' => $modelChangedDocuments,
                'scanFile' => $tables['scan'],
                'docFiles' => $tables['docs'],
            ]);
        }
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentOrder::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }
    public function actionDelete($id){
        $model = $this->repository->get($id);
        $number = $model->order_number;
        if ($model) {
            $this->repository->delete($model);
            Yii::$app->session->setFlash('success', "Документ $number успешно удален");
            return $this->redirect(['index']);
        }
        else {
            throw new DomainException('Модель не найдена');
        }
    }
    public function actionView($id){
        $modelResponsiblePeople = implode('<br>',
            $this->documentOrderService->createOrderPeopleArray(
                $this->orderPeopleRepository->getResponsiblePeople($id)
            )
        );
        $modelChangedDocuments = implode('<br>',
            $this->service->createChangedDocumentsArray(
                $this->expireRepository->getExpireByActiveRegulationId($id)
            )
        );
        return $this->render('view', [
            'model' => $this->repository->get($id),
            'modelResponsiblePeople' => $modelResponsiblePeople,
            'modelChangedDocuments' => $modelChangedDocuments
        ]);
    }

    public function actionDeleteDocument($id, $modelId)
    {
        $this->expireRepository->deleteByActiveRegulationId($id);
        return $this->redirect(['update', 'id' => $modelId]);
    }
}