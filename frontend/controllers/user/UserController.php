<?php

namespace frontend\controllers\user;

use frontend\models\search\SearchUser;
use Yii;
use yii\web\Controller;

class UserController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new SearchUser();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}