<?php

namespace frontend\controllers\educational;

use common\controllers\DocumentController;
use frontend\models\search\SearchTrainingGroup;
use Yii;

class TrainingGroupController extends DocumentController
{
    public function actionIndex($archive = null)
    {
        $searchModel = new SearchTrainingGroup();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}