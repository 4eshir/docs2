<?php

namespace frontend\controllers\educational;

use common\controllers\DocumentController;
use common\helpers\html\HtmlBuilder;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use frontend\events\educational\training_program\CreateTrainingProgramBranchEvent;
use frontend\models\search\SearchTrainingProgram;
use frontend\models\work\educational\AuthorProgramWork;
use frontend\models\work\educational\ThematicPlanWork;
use frontend\models\work\educational\TrainingProgramWork;
use frontend\services\educational\TrainingProgramService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TrainingProgramController implements the CRUD actions for TrainingProgram model.
 */
class TrainingProgramController extends DocumentController
{
    private TrainingProgramService $service;
    private TrainingProgramRepository $repository;

    public function __construct($id, $module, TrainingProgramService $service, TrainingProgramRepository $repository, $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->service = $service;
        $this->repository = $repository;
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
        $modelAuthor = [new AuthorProgramWork];
        $modelThematicPlan = [new ThematicPlanWork];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->service->getFilesInstances($model);
            $this->repository->save($model);
            $this->service->saveFilesFromModel($model);
            $this->service->saveUtpFromFile($model);

            $model->recordEvent(new CreateTrainingProgramBranchEvent($model->id, $model->branches), TrainingProgramWork::class);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelAuthor' => $modelAuthor,
            'modelThematicPlan' => $modelThematicPlan,
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
        $modelAuthor = $this->repository->getAuthors($id);
        $themes = $this->repository->getThematicPlan($id);
        $modelThematicPlan = HtmlBuilder::createTableWithActionButtons(
            [
                ArrayHelper::getColumn($themes, 'theme')
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Редактировать',
                    Url::to('update-theme'),
                    ['id' => ArrayHelper::getColumn($themes, 'id'), 'modelId' => ArrayHelper::getColumn($themes, 'training_program_id')]),
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-theme'),
                    ['id' => ArrayHelper::getColumn($themes, 'id'), 'modelId' => ArrayHelper::getColumn($themes, 'training_program_id')])
            ]
        );

        if ($model->load(Yii::$app->request->post())) {
            $this->service->getFilesInstances($model);
            $this->repository->save($model);
            $this->service->saveFilesFromModel($model);
            $this->service->saveUtpFromFile($model);

            $model->recordEvent(new CreateTrainingProgramBranchEvent($model->id, $model->branches), TrainingProgramWork::class);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'modelAuthor' => $modelAuthor,
            'modelThematicPlan' => $modelThematicPlan,
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
            $group = TrainingProgramWork::find()->where(['id' => $modelId])->one();
            $modelAuthor = [new AuthorProgramWork];
            $modelThematicPlan = [new ThematicPlanWork];
            return $this->render('update', [
                'model' => $group,
                'modelAuthor' => $modelAuthor,
                'modelThematicPlan' => $modelThematicPlan,
            ]);
        }
        return $this->render('update-plan', [
            'model' => $model,
        ]);
    }

    public function actionSaver()
    {
        $checks = Yii::$app->request->post('selection');
        $allTps = TrainingProgramWork::find()->all();
        foreach ($allTps as $allTp)
        {
            $allTp->actual = 0;
            $allTp->save(false);
        }
        if ($checks !== null)
            foreach ($checks as $check)
            {
                $tp = TrainingProgramWork::find()->where(['id' => $check])->one();
                $tp->actual = 1;
                $tp->save(false);

            }
        return $this->redirect(['/training-program/index']);
    }

    public function actionActual($id)
    {
        $tag = TrainingProgramWork::findOne($id);
        $tag->actual === 1 ? $tag->actual = 0 : $tag->actual = 1;
        $tag->save(false);
        if ($tag->actual === 0)
            Yii::$app->session->setFlash("warning", "Программа ".$tag->name." больше не актуальна");
        else
            Yii::$app->session->setFlash("success", "Программа ".$tag->name." теперь актуальна");
        return $this->redirect(['/training-program/index']);
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = TrainingProgramWork::find()->where(['id' => $modelId])->one();

        if ($type == 'doc')
        {
            $model->doc_file = '';
            $model->save(false);
            return $this->redirect('index?r=training-program/update&id='.$model->id);
        }

        if ($type == 'contract')
        {
            $model->contract = '';
            $model->save(false);
            return $this->redirect('index?r=training-program/update&id='.$model->id);
        }


        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = explode(" ", $model->edit_docs);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            $model->edit_docs = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=training-program/update&id='.$model->id);
    }

    public function actionDeleteAuthor($peopleId, $modelId)
    {
        $resp = AuthorProgramWork::find()->where(['author_id' => $peopleId])->andWhere(['training_program_id' => $modelId])->one();
        $name = $resp->authorWork->shortName;
        $program = $resp->trainingProgram->name;
        if ($resp != null)
            $resp->delete();
        $model = $this->findModel($modelId);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален автор ' . $name . ' образовательной программы ' . $program);

        return $this->redirect('index.php?r=training-program/update&id='.$modelId);
    }

    public function actionDeleteTheme($id, $modelId)
    {
        $plan = ThematicPlanWork::find()->where(['id' => $id])->one();
        $name = $plan->trainingProgram->name;
        $plan->delete();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалена тема УТП образовательной программы ' . $name);

        return $this->redirect('index?r=training-program/update&id='.$modelId);
    }

    public function actionAmnesty ($id)
    {
        $errorsAmnesty = new ProgramErrorsWork();
        $errorsAmnesty->ProgramAmnesty($id);
        return $this->redirect('index?r=training-program/view&id='.$id);
    }

    public function actionArchive($arch, $unarch)
    {
        $arch = explode(',', $arch);
        $unarch = explode(',', $unarch);

        for ($i = 0; $i < count($arch) && $arch[0] != ''; $i++)
        {
            $tag = TrainingProgramWork::findOne($arch[$i]);
            if ($tag->actual != 1)
            {
                $tag->actual = 1;
                $tag->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Программа '.$tag->name.' (id: '.$tag->id.') теперь актуальна');
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменена образовательная программа '.$model->name);
            }

        }

        for ($i = 0; $i < count($unarch) && $unarch[0] != ''; $i++)
        {
            $tag = TrainingProgramWork::findOne($unarch[$i]);
            if ($tag->actual != 0)
            {
                $tag->actual = 0;
                $tag->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Программа '.$tag->name.' (id: '.$tag->id.') больше не актуальна');
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменена образовательная программа '.$model->name);
            }

        }
        /*
                $selections = explode(',', $ids);
                $flashStr = "";
                $allPrograms = TrainingProgramWork::find()->all();
                //$errors = new GroupErrorsWork();
                foreach ($allPrograms as $program) {
                    if (!$this->InArray($program->id, $selections) && $program->actual == 1)
                        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Программа '.$program->name.' (id: '.$program->id.') больше не актуальна');
                    if ($this->InArray($program->id, $selections) && $program->actual == 0)
                        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Программа '.$program->name.' (id: '.$program->id.') теперь актуальна');
                    $program->actual = 0;
                    $program->isCod = 2;
                    $program->save(false);
                    //var_dump($program->getErrors());
                }
                if ($ids !== "")
                    for ($i = 0; $i < count($selections); $i++)
                    {
                        $tag = TrainingProgramWork::findOne($selections[$i]);
                        $tag->archStat = 1;
                        $tag->actual === 1 ? $tag->actual = 0 : $tag->actual = 1;
                        $tag->save(false);
                        if ($tag->actual === 0)
                            $flashStr .= "Программа ".$tag->name." больше не актуальна\n";
                        else
                            $flashStr .= "Программа ".$tag->name." теперь актуальна\n";

                        //$errors->CheckArchiveTrainingGroup($tag->id);
                    }*/
        Yii::$app->session->setFlash("success", 'Изменение статуса программ произведено успешно');
        return $this->redirect(['/training-program/index']);
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

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
