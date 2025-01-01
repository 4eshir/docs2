<?php

namespace frontend\controllers\order;

use app\models\search\SearchOrderTraining;
use app\models\work\order\OrderTrainingWork;
use common\controllers\DocumentController;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use Yii;

class OrderTrainingController extends DocumentController
{
    private PeopleRepository $peopleRepository;
    public function __construct(
        $id,
        $module,
        PeopleRepository $peopleRepository,
        FileService $fileService,
        FilesRepository $filesRepository,
        $config = []
    )
    {
        $this->peopleRepository = $peopleRepository;
        parent::__construct($id, $module, $fileService, $filesRepository, $config);
    }
    public function actionIndex(){
        $searchModel = new SearchOrderTraining();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id){
        return $this->render('view', [
            'id' => $id
        ]);
    }
    public function actionCreate(){

        $model = new OrderTrainingWork();
        $people = $this->peopleRepository->getOrderedList();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
            'people' => $people,
        ]);


    }
}