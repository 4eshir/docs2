<?php

namespace frontend\controllers\dictionaries;

use common\repositories\dictionaries\PositionRepository;
use DomainException;
use frontend\models\search\SearchPosition;
use frontend\models\work\dictionaries\PositionWork;
use frontend\services\dictionaries\PositionService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PositionController implements the CRUD actions for Position model.
 */
class PositionController extends Controller
{
    private PositionRepository $repository;
    private PositionService $service;

    public function __construct($id, $module, PositionRepository $repository, PositionService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->service = $service;
    }

    public function actionIndex()
    {
        $searchModel = new SearchPosition();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->repository->get($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new PositionWork();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->repository->save($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->repository->get($id);
        /** @var PositionWork $model */

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->repository->save($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        /** @var PositionWork $model */
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->repository->delete($model);
            Yii::$app->session->addFlash('success', 'Должность "'.$model->name.'" успешно удалена');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        /*if (Yii::$app->rac->isGuest() || !Yii::$app->rac->checkUserAccess(Yii::$app->rac->authId(), get_class(Yii::$app->controller), $action)) {
            Yii::$app->session->setFlash('error', 'У Вас недостаточно прав. Обратитесь к администратору для получения доступа');
            $this->redirect(Yii::$app->request->referrer);
            return false;
        }*/

        return parent::beforeAction($action); 
    }
}
