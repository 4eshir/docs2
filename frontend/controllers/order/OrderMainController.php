<?php

namespace frontend\controllers\order;

use app\models\work\general\OrderPeopleWork;
use app\models\work\order\ExpireWork;
use common\helpers\DateFormatter;
use common\repositories\dictionaries\PeopleRepository;
use app\models\work\order\OrderMainWork;
use common\repositories\general\OrderPeopleRepository;
use DomainException;
use frontend\events\expire\ExpireCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\models\search\SearchOrderMain;
use common\repositories\order\OrderMainRepository;
use yii\web\Controller;
use yii;

class OrderMainController extends Controller
{
    private OrderMainRepository $repository;
    private PeopleRepository $peopleRepository;
    public function __construct(
        $id,
        $module,
        OrderMainRepository $repository,
        PeopleRepository $peopleRepository,
        $config = [])
    {
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
        $docs = $model->getDocumentExpire($post);
        $regulation = $model->getRegulationExpire($post);
        $statuses = $model->getStatusExpire($post);
        if ($model->load($post)) {
            $respPeople = $model->getResponsiblePeople($post);
            $model->order_copy_id = 1;
            $model->order_date = DateFormatter::format( $model->order_date, DateFormatter::dmY_dot, DateFormatter::Ymd_dash);
            if(!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->repository->save($model);
            for($i = 1; $i < count($docs); $i++){
                $model->recordEvent(new ExpireCreateEvent($model->id,
                    $regulation[$i],$docs[$i],1,$statuses[$i]), ExpireWork::class);
            }
            for($i = 1; $i < count($respPeople); $i++){
                $model->recordEvent(new OrderPeopleCreateEvent($respPeople[$i], $model->id), OrderPeopleWork::class );
            }

            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'bringPeople' => $bringPeople
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

        ]);
    }

}