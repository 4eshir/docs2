<?php

namespace frontend\controllers\order;

use common\repositories\dictionaries\PeopleRepository;

use frontend\models\search\SearchOrderMain;
use frontend\models\work\order\OrderMainWork;
=======
use app\models\work\order\OrderMainWork;
use common\repositories\order\OrderMainRepository;
use yii\web\Controller;
>>>>>>> 27809e3aee8427827a25535b47e2218845c3921a
use yii;
use yii\web\Controller;

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