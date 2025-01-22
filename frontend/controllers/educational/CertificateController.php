<?php

namespace frontend\controllers\educational;

use frontend\forms\certificate\CertificateForm;
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

    public function actionCreate()
    {
        $form = new CertificateForm();

        if ($form->load(Yii::$app->request->post())) {

            return $this->redirect(['view', 'id' => $form->id]);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }
}