<?php

namespace frontend\controllers\educational;

use common\controllers\DocumentController;
use common\Model;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\models\search\SearchTrainingGroup;
use frontend\models\work\general\PeopleWork;
use frontend\services\educational\TrainingGroupService;
use Yii;

class TrainingGroupController extends DocumentController
{
    private TrainingGroupService $service;
    private TrainingProgramRepository $trainingProgramRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    private PeopleRepository $peopleRepository;

    public function __construct(
        $id,
        $module,
        FileService $fileService,
        FilesRepository $filesRepository,
        TrainingGroupService $service,
        TrainingProgramRepository $trainingProgramRepository,
        TrainingGroupRepository $trainingGroupRepository,
        PeopleRepository $peopleRepository,
        $config = [])
    {
        parent::__construct($id, $module, $fileService, $filesRepository, $config);
        $this->service = $service;
        $this->trainingProgramRepository = $trainingProgramRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->peopleRepository = $peopleRepository;
    }


    public function actionIndex($archive = null)
    {
        $searchModel = new SearchTrainingGroup();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $form = new TrainingGroupBaseForm();
        $modelTeachers = [new PeopleWork];
        $programs = $this->trainingProgramRepository->getAll();
        $people = $this->peopleRepository->getPeopleFromMainCompany();

        if ($form->load(Yii::$app->request->post())) {
            $modelTeachers = Model::createMultiple(PeopleWork::classname());
            Model::loadMultiple($modelTeachers, Yii::$app->request->post());
            $form->teachers = $modelTeachers;
            $groupModel = $this->service->convertBaseFormToModel($form);
            $form->id = $this->trainingGroupRepository->save($groupModel);

            $this->service->getFilesInstances($form);
            $this->service->saveFilesFromModel($form);

            return $this->render('view', [
                'model' => $groupModel
            ]);
        }

        return $this->render('create', [
            'model' => $form,
            'modelTeachers' => $modelTeachers,
            'trainingPrograms' => $programs,
            'people' => $people,
        ]);
    }
}