<?php

namespace frontend\controllers\document;

use common\models\search\SearchDocumentIn;
use Yii;
use yii\web\Controller;

class DocumentInController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new SearchDocumentIn();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}