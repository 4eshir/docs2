<?php

namespace frontend\controllers\event;

use frontend\forms\event\ForeignEventForm;
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
        $form = new ForeignEventForm($id);

        if ($form->load(Yii::$app->request->post())) {
            // что-то делаем с данными
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $form
        ]);
    }

    public function actionView($id)
    {
        $form = new ForeignEventForm($id);
        return $this->render('view',[
            'model' => $form
        ]);
    }
}