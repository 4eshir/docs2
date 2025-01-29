<?php
namespace frontend\controllers\order;
use app\components\DynamicWidget;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\DocumentOrderWork;
use app\models\work\order\ExpireWork;
use app\models\work\order\OrderMainWork;
use app\services\order\OrderMainService;
use common\controllers\DocumentController;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\models\scaffold\OrderMain;
use common\repositories\dictionaries\PeopleRepository;
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
use frontend\models\work\general\PeopleStampWork;
use frontend\models\work\regulation\RegulationWork;
use PHPUnit\Util\Xml\ValidationResult;
use yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;

class OrderMainController extends Controller
{
    private OrderMainRepository $repository;
    private DocumentOrderRepository $documentOrderRepository;
    private OrderMainService $service;
    private ExpireRepository $expireRepository;
    private PeopleStampRepository $peopleStampRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private UserRepository $userRepository;
    private RegulationRepository $regulationRepository;
    private FileService $fileService;
    private FilesRepository $filesRepository;

    public function __construct(
        $id,
        $module,
        OrderMainRepository $repository,
        DocumentOrderRepository $documentOrderRepository,
        OrderMainService $service,
        FileService $fileService,
        ExpireRepository $expireRepository,
        FilesRepository $filesRepository,
        PeopleStampRepository $peopleStampRepository,
        OrderPeopleRepository $orderPeopleRepository,
        UserRepository $userRepository,
        RegulationRepository $regulationRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->documentOrderRepository = $documentOrderRepository;
        $this->expireRepository = $expireRepository;
        $this->filesRepository = $filesRepository;
        $this->fileService = $fileService;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->userRepository = $userRepository;
        $this->regulationRepository = $regulationRepository;
        $this->repository = $repository;

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
            $this->service->getFilesInstances($model);

            $this->service->addExpireEvent($modelExpire, $model);
            //сделать
            $this->service->addOrderPeopleEvent($respPeople, $model);
            $this->service->saveFilesFromModel($model);
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
        /* @var OrderMainWork $model */
        $model = $this->repository->get($id);
        $bringPeople = $this->peopleRepository->getOrderedList();
        $post = Yii::$app->request->post();
        $orders = OrderMainWork::find()->all();
        $regulations = RegulationWork::find()->all();
        $modelResponsiblePeople = $this->service->getResponsiblePeopleTable($model->id);
        $modelChangedDocuments = $this->service->getChangedDocumentsTable($model->id);
        $tables = $this->service->getUploadedFilesTables($model);
        if($model->load($post)){
            $model->type = OrderMainWork::ORDER_MAIN;
            $respPeople = DynamicWidget::getData(basename(OrderMainWork::class), "names", $post);
            $docs = DynamicWidget::getData(basename(OrderMainWork::class), "orders", $post);
            $regulation = DynamicWidget::getData(basename(OrderMainWork::class), "regulations", $post);
            $status = DynamicWidget::getData(basename(OrderMainWork::class), "status", $post);
            if(!$model->validate()){
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->repository->save($model);
            $this->service->getFilesInstances($model);
            $this->service->addExpireEvent($docs, $regulation, $status, $model);
            $this->service->addOrderPeopleEvent($respPeople, $model);
            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('update', [
            'orders' => $orders,
            'model' => $model,
            'bringPeople' => $bringPeople,
            'regulations' => $regulations,
            'modelResponsiblePeople' => $modelResponsiblePeople,
            'modelChangedDocuments' => $modelChangedDocuments,
            'scanFile' => $tables['scan'],
            'docFiles' => $tables['docs'],
        ]);
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
            $this->service->createOrderPeopleArray(
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
            $file = $this->filesRepository->getById($fileId);
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
    public function actionDeleteDocument($id, $modelId)
    {
        $this->expireRepository->deleteByActiveRegulationId($id);
        return $this->redirect(['update', 'id' => $modelId]);
    }
}