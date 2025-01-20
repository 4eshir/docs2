<?php

namespace frontend\controllers\event;

use frontend\models\search\SearchForeignEvent;
use Yii;
use yii\web\Controller;

class ForeignEventController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new SearchForeignEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        
    }
}