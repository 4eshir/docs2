<?php
namespace frontend\controllers\order;
use app\models\work\general\OrderPeopleWork;
use app\models\work\order\ExpireWork;
use common\helpers\DateFormatter;
use common\repositories\dictionaries\PeopleRepository;
use app\models\work\order\OrderMainWork;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\expire\ExpireCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\models\search\SearchOrderMain;
use common\repositories\order\OrderMainRepository;
use frontend\models\work\regulation\RegulationWork;
use yii\web\Controller;
use yii;
class OrderMainController extends Controller
{
    private OrderMainRepository $repository;
    private FileService $fileService;
    private FilesRepository $filesRepository;
    private PeopleRepository $peopleRepository;
    public function __construct(
        $id,
        $module,
        OrderMainRepository $repository,
        PeopleRepository $peopleRepository,
        FileService          $fileService,
        FilesRepository      $filesRepository,
        $config = [])
    {
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
        $this->peopleRepository = $peopleRepository;
        $this->repository = $repository;
        parent::__construct($id, $module, $config);
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
            $respPeople = $model->getResponsiblePeople($post);
            //$statuses = $model->getStatusExpire($post);
            $docs = $model->getDocumentExpire($post);
            $regulation = $model->getRegulationExpire($post);
            $model->order_copy_id = 1;
            $model->order_date = DateFormatter::format($model->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
            if(!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->repository->save($model);
            if($docs[0] != NULL && $regulation[0] != NULL){
                for($i = 0; $i < count($docs); $i++){
                        $model->recordEvent(new ExpireCreateEvent($regulation[$i],
                            $regulation[$i],$docs[$i],1,1), ExpireWork::class);
                }
            }
            if($respPeople[0] != NULL) {
                for ($i = 0; $i < count($respPeople); $i++) {
                    $model->recordEvent(new OrderPeopleCreateEvent($respPeople[$i], $model->id), OrderPeopleWork::class);
                }
            }
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

}