<?php

namespace frontend\controllers\order;

use common\repositories\dictionaries\PeopleRepository;
use app\models\work\order\OrderMainWork;
use common\repositories\dictionaries\PeopleRepository;
use DomainException;
use frontend\models\search\SearchOrderMain;
use common\repositories\order\OrderMainRepository;
use frontend\models\search\SearchOrderMain;
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
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->repository->save($model);
        }
        return $this->render('create', [
            'model' => $model,
            'bringPeople' => $bringPeople
        ]);
    }

}