<?php

namespace frontend\controllers\regulation;

use common\controllers\DocumentController;
use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\models\search\SearchRegulation;
use frontend\models\work\regulation\RegulationWork;
use frontend\services\regulation\RegulationService;
use Yii;

class RegulationController extends DocumentController
{
    private RegulationRepository $repository;
    private RegulationService $service;

    public function __construct(
                             $id,
                             $module,
        RegulationRepository $repository,
        RegulationService    $service,
                             $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->repository = $repository;
        $this->service = $service;
    }

    public function actionIndex()
    {
        $searchModel = new SearchRegulation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->repository->get($id)
        ]);
    }

    public function actionCreate()
    {
        $model = new RegulationWork();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->service->getFilesInstances($model);
            $this->repository->save($model);

            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    //
    public function actionUpdate($id)
    {
        $model = $this->repository->get($id);
        /** @var RegulationWork $model */
        $fileTables = $this->service->getUploadedFilesTables($model);

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->service->getFilesInstances($model);
            $this->repository->save($model);

            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'scanFile' => $fileTables['scan'],
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        $name = $model->name;
        if ($model) {
            $this->repository->delete($model);
            Yii::$app->session->setFlash('success', "Положение \"$name\" успешно удалено");
            return $this->redirect(['index']);
        }
        else {
            throw new DomainException('Модель не найдена');
        }
    }

    public function beforeAction($action)
    {
        if (Yii::$app->rac->isGuest() || !Yii::$app->rac->checkUserAccess(Yii::$app->rac->authId(), get_class(Yii::$app->controller), $action)) {
            Yii::$app->session->setFlash('error', 'У Вас недостаточно прав. Обратитесь к администратору для получения доступа');
            $this->redirect(Yii::$app->request->referrer);
            return false;
        }

        return parent::beforeAction($action); 
    }
}