<?php

namespace frontend\controllers\order;

use common\models\scaffold\People;
use common\models\search\SearchOrderMain;
use common\repositories\dictionaries\PeopleRepository;
use frontend\models\work\order\OrderMainWork;
use yii\web\Controller;
use yii;
class OrderMainController extends Controller
{
    private PeopleRepository $peopleRepository;
    public function __construct(
        $id,
        $module,
        PeopleRepository $repository,
        $config = [])
    {
        $this->peopleRepository = $repository;
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
            var_dump(Yii::$app->request->post());

        }

        return $this->render('create', [
            'model' => $model,
            'bringPeople' => $bringPeople
        ]);
    }

}