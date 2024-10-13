<?php
namespace frontend\controllers\order;
use app\components\DynamicWidget;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\OrderMainWork;
use app\services\order\OrderMainService;
use common\controllers\DocumentController;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use common\repositories\order\OrderMainRepository;

use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\helpers\HeaderWizard;
use frontend\models\search\SearchOrderMain;
use frontend\models\work\general\FilesWork;
use frontend\models\work\regulation\RegulationWork;
use yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;

class OrderMainController extends Controller
{
    private OrderMainRepository $repository;
    private OrderMainService $service;
    private PeopleRepository $peopleRepository;
    private FileService $fileService;
    private FilesRepository $filesRepository;

    public function __construct(
        $id,
        $module,
        OrderMainRepository $repository,
        OrderMainService $service,
        FileService $fileService,
        FilesRepository $filesRepository,
        PeopleRepository $peopleRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->filesRepository = $filesRepository;
        $this->fileService = $fileService;
        $this->peopleRepository = $peopleRepository;
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
        $bringPeople = $this->peopleRepository->getOrderedList();
        $post = Yii::$app->request->post();
        $orders = OrderMainWork::find()->all();
        $regulations = RegulationWork::find()->all();

        if ($model->load($post)) {

            //beforeValidate
            //$model->order_copy_id = 1;
            $respPeople = DynamicWidget::getData(OrderPeopleWork::class, "names", $post);
            $docs = DynamicWidget::getData(OrderPeopleWork::class, "orders", $post);
            $regulation = DynamicWidget::getData(OrderMainWork::class, "regulations", $post);
            //beforeValidate
            //$model->order_date = DateFormatter::format($model->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
            if(!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->service->getFilesInstances($model);
            $this->repository->save($model);
            $this->service->addExpireEvent($docs, $regulation, $model);
            $this->service->addOrderPeopleEvent($respPeople, $model);
            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'orders' => $orders,
            'model' => $model,
            'bringPeople' => $bringPeople,
            'regulations' => $regulations,
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
        return $this->render('view', [
            'model' => $this->repository->get($id),
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
            $file->recordEvent(new FileDeleteEvent($file->filepath), get_class($file));
            $file->releaseEvents();

            Yii::$app->session->setFlash('success', "Файл $filepath успешно удален");
            return $this->redirect(['update', 'id' => $modelId]);
        }
        catch (DomainException $e) {
            return 'Oops! Something wrong';
        }
    }
}