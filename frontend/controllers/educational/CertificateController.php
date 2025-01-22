<?php

namespace frontend\controllers\educational;

use frontend\models\search\SearchCertificate;
use Yii;
use yii\web\Controller;

class CertificateController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new SearchCertificate();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}