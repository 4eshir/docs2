<?php

namespace frontend\controllers\order;

use common\models\search\SearchDocumentIn;
use common\models\search\SearchOrderMain;
use yii\web\Controller;
use yii;
class OrderMainController extends Controller
{
    public function __construct(
        $id,
        $module,
        $config = [])
    {
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
        return $this->render('create');
    }

}