<?php

namespace frontend\controllers\educational;

use app\components\DynamicWidget;
use common\controllers\DocumentController;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\educational\training_program\CreateTrainingProgramBranchEvent;
use frontend\models\search\SearchTrainingProgram;
use frontend\models\work\educational\training_program\ThematicPlanWork;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use frontend\services\educational\TrainingProgramService;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * TrainingProgramController implements the CRUD actions for TrainingProgram model.
 */
class TrainingProgramController extends DocumentController
{
    private TrainingProgramService $service;
    private TrainingProgramRepository $repository;
    private PeopleRepository $peopleRepository;

    public function __construct(
        $id,
        $module,
        TrainingProgramService $service,
        TrainingProgramRepository $repository,
        PeopleRepository $peopleRepository,
        $config = []
    )
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->service = $service;
        $this->repository = $repository;
        $this->peopleRepository = $peopleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TrainingProgram models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchTrainingProgram();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrainingProgram model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->repository->get($id),
            'thematicPlan' => $this->repository->getThematicPlan($id),
        ]);
    }

    /**
     * Creates a new TrainingProgram model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrainingProgramWork();
        $ourPeople = $this->peopleRepository->getPeopleFromMainCompany();

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $postThemes = DynamicWidget::getData(basename(TrainingProgramWork::class), 'themes', $post);
            $postControls = DynamicWidget::getData(basename(TrainingProgramWork::class), 'controls', $post);
            $postAuthors = DynamicWidget::getData(basename(TrainingProgramWork::class), 'authors', $post);
            $this->service->getFilesInstances($model);
            $this->repository->save($model);

            $this->service->attachUtp($model, $postThemes, $postControls);
            $this->service->saveFilesFromModel($model);
            $this->service->saveUtpFromFile($model);
            $this->service->attachAuthors($model, $postAuthors);

            $model->recordEvent(new CreateTrainingProgramBranchEvent($model->id, $model->branches), TrainingProgramWork::class);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'ourPeople' => $ourPeople,
        ]);
    }

    /**
     * Updates an existing TrainingProgram model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        /** @var TrainingProgramWork $model */
        $model = $this->repository->get($id);
        $authors = $this->repository->getAuthors($id);
        $themes = $this->repository->getThematicPlan($id);
        $fileTables = $this->service->getUploadedFilesTables($model);
        $depTables = $this->service->getDependencyTables($authors, $themes);
        $ourPeople = $this->peopleRepository->getPeopleFromMainCompany();

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $postThemes = DynamicWidget::getData(basename(TrainingProgramWork::class), 'themes', $post);
            $postControls = DynamicWidget::getData(basename(TrainingProgramWork::class), 'controls', $post);
            $postAuthors = DynamicWidget::getData(basename(TrainingProgramWork::class), 'authors', $post);
            $this->service->getFilesInstances($model);
            $this->repository->save($model);

            $this->service->attachUtp($model, $postThemes, $postControls);
            $this->service->saveFilesFromModel($model);
            $this->service->saveUtpFromFile($model);
            $this->service->attachAuthors($model, $postAuthors);

            $model->recordEvent(new CreateTrainingProgramBranchEvent($model->id, $model->branches), TrainingProgramWork::class);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'ourPeople' => $ourPeople,
            'modelAuthor' => $depTables['authors'],
            'modelThematicPlan' => $depTables['themes'],
            'mainFile' => $fileTables['main'],
            'docFiles' => $fileTables['doc'],
            'contractFile' => $fileTables['contract'],
        ]);
    }

    /**
     * Deletes an existing TrainingProgram model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /** @var TrainingProgramWork $model */
        $model = $this->repository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->repository->delete($model);
            Yii::$app->session->addFlash('success', 'Образовательная программа "'.$model->name.'" успешно удалена');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionUpdateTheme($id, $modelId)
    {
        /** @var ThematicPlanWork $model */
        $model = $this->repository->getTheme($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->repository->saveTheme($model);
            return $this->redirect(['educational/training-program/update', 'id' => $modelId]);
        }
        return $this->render('update-theme', [
            'model' => $model,
        ]);
    }

    public function actionDeleteTheme($id, $modelId)
    {
        /** @var ThematicPlanWork $plan */
        //$name = $plan->trainingProgramWork->name;
        $this->repository->deleteTheme($id);

        return $this->redirect(['educational/training-program/update', 'id' => $modelId]);
    }

    public function actionDeleteAuthor($id, $modelId)
    {
        $this->repository->deleteAuthor($id);

        return $this->redirect(['educational/training-program/update', 'id' => $modelId]);
    }

    private function InArray($id, $array)
    {
        for ($i = 0; $i < count($array); $i++)
            if ($id == $array[$i])
                return true;
        return false;
    }

    //Проверка на права доступа к CRUD-операциям
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
