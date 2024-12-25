<?php

namespace frontend\controllers\educational;

use common\controllers\DocumentController;
use common\helpers\common\RequestHelper;
use common\helpers\html\HtmlBuilder;
use common\Model;
use common\models\scaffold\TrainingGroup;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\educational\training_group\AddTeachersToGroupEvent;
use frontend\forms\training_group\PitchGroupForm;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\forms\training_group\TrainingGroupCombinedForm;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\forms\training_group\TrainingGroupScheduleForm;
use frontend\models\search\SearchTrainingGroup;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\ProjectThemeWork;
use frontend\services\educational\TrainingGroupService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class TrainingGroupController extends DocumentController
{
    private TrainingGroupService $service;
    private TrainingProgramRepository $trainingProgramRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    private TrainingGroupLessonRepository $groupLessonRepository;
    private ForeignEventParticipantsRepository $participantsRepository;
    private PeopleRepository $peopleRepository;

    public function __construct(
        $id,
        $module,
        FileService $fileService,
        FilesRepository $filesRepository,
        TrainingGroupService $service,
        TrainingProgramRepository $trainingProgramRepository,
        TrainingGroupRepository $trainingGroupRepository,
        TrainingGroupLessonRepository $groupLessonRepository,
        ForeignEventParticipantsRepository $participantsRepository,
        PeopleRepository $peopleRepository,
        $config = [])
    {
        parent::__construct($id, $module, $fileService, $filesRepository, $config);
        $this->service = $service;
        $this->trainingProgramRepository = $trainingProgramRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->groupLessonRepository = $groupLessonRepository;
        $this->participantsRepository = $participantsRepository;
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
        $modelTeachers = [new TeacherGroupWork];
        $programs = $this->trainingProgramRepository->getAll();
        $people = $this->peopleRepository->getPeopleFromMainCompany();

        if ($form->load(Yii::$app->request->post())) {
            if (!$form->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($form->getErrors()));
            }
            $groupModel = $this->service->convertBaseFormToModel($form);

            $modelTeachers = Model::createMultiple(TeacherGroupWork::classname());
            Model::loadMultiple($modelTeachers, Yii::$app->request->post());
            if (Model::validateMultiple($modelTeachers, ['id'])) {
                $form->teachers = $modelTeachers;
                $groupModel->generateNumber($this->peopleRepository->get($form->teachers[0]->peopleId));
            }
            else {
                $groupModel->generateNumber('');
            }

            $form->id = $this->trainingGroupRepository->save($groupModel);
            $this->service->attachTeachers($form, $form->teachers);

            $this->service->getFilesInstances($form);
            $this->service->saveFilesFromModel($form);
            $form->releaseEvents();

            return $this->redirect(['view', 'id' => $groupModel->id]);
        }

        return $this->render('create', [
            'model' => $form,
            'modelTeachers' => $modelTeachers,
            'trainingPrograms' => $programs,
            'people' => $people,
        ]);
    }

    public function actionBaseForm($id)
    {
        $formBase = new TrainingGroupBaseForm($id);
        $programs = $this->trainingProgramRepository->getAll();
        $people = $this->peopleRepository->getPeopleFromMainCompany();
        $tables = $this->service->getUploadedFilesTables($formBase);

        if ($formBase->load(Yii::$app->request->post())) {
            if (!$formBase->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($formBase->getErrors()));
            }
            $groupModel = $this->service->convertBaseFormToModel($formBase);

            $modelTeachers = Model::createMultiple(TeacherGroupWork::classname());
            Model::loadMultiple($modelTeachers, Yii::$app->request->post());
            if (Model::validateMultiple($modelTeachers, ['peopleId'])) {
                $formBase->teachers = $modelTeachers;
                $groupModel->generateNumber($this->peopleRepository->get($formBase->teachers[0]->peopleId));
            }
            else {
                $groupModel->generateNumber('');
            }

            $formBase->id = $this->trainingGroupRepository->save($groupModel);
            $this->service->attachTeachers($formBase, $formBase->teachers);

            $this->service->getFilesInstances($formBase);
            $this->service->saveFilesFromModel($formBase);
            $formBase->releaseEvents();

            return $this->redirect(['view', 'id' => $groupModel->id]);
        }

        return $this->render('_form-base', [
            'model' => $formBase,
            'modelTeachers' => count($formBase->teachers) > 0 ? $formBase->teachers : [new TeacherGroupWork],
            'trainingPrograms' => $programs,
            'people' => $people,
            'photos' => $tables['photos'],
            'presentations' => $tables['presentations'],
            'workMaterials' => $tables['workMaterials'],
        ]);
    }

    public function actionParticipantForm($id)
    {
        $formParticipant = new TrainingGroupParticipantForm($id);
        $childs = $this->participantsRepository->getSortedList(ForeignEventParticipantsRepository::SORT_FIO);

        if (count(Yii::$app->request->post()) > 0) {
            $modelChilds = Model::createMultiple(TrainingGroupParticipantWork::classname());
            Model::loadMultiple($modelChilds, Yii::$app->request->post());
            if (Model::validateMultiple($modelChilds, ['id', 'participant_id', 'send_method'])) {
                $formParticipant->participants = $modelChilds;
            }

            $this->service->attachParticipants($formParticipant);
            $formParticipant->releaseEvents();

            return $this->redirect(['view', 'id' => $formParticipant->id]);
        }

        return $this->render('_form-participant', [
            'model' => $formParticipant,
            'modelChilds' => count($formParticipant->participants) > 0 ? $formParticipant->participants : [new TrainingGroupParticipantWork],
            'childs' => $childs
        ]);
    }

    public function actionScheduleForm($id)
    {
        $formData = $this->service->prepareFormScheduleData($id);
        $formSchedule = $formData['formSchedule'];
        $modelLessons = $formData['modelLessons'];
        $auditoriums = $formData['auditoriums'];
        $scheduleTable = $formData['scheduleTable'];

        if ($formSchedule->load(Yii::$app->request->post())) {
            $modelLessons = Model::createMultiple(TrainingGroupLessonWork::classname());
            Model::loadMultiple($modelLessons, Yii::$app->request->post());
            if (Model::validateMultiple($modelLessons, ['lesson_date', 'lesson_start_time', 'branch', 'auditorium_id', 'autoDate'])) {
                $formSchedule->lessons = $modelLessons;
            }

            if (!$formSchedule->isManual()) {
                $formSchedule->convertPeriodToLessons();
            }

            $this->service->preprocessingLessons($formSchedule);
            $this->service->attachLessons($formSchedule);
            $formSchedule->releaseEvents();

            return $this->redirect(['view', 'id' => $formSchedule->id]);
        }

        return $this->render('_form-schedule', [
            'model' => $formSchedule,
            'modelLessons' => count($modelLessons) > 0 ? $modelLessons : [new TrainingGroupParticipantWork],
            'auditoriums' => $auditoriums,
            'scheduleTable' => $scheduleTable
        ]);
    }

    public function actionPitchForm($id)
    {
        $formPitch = new PitchGroupForm($id);
        $peoples = $this->peopleRepository->getPeopleFromMainCompany();

        if ($formPitch->load(Yii::$app->request->post())) {
            if (!$formPitch->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($formPitch->getErrors()));
            }

            $modelThemes = Model::createMultiple(ProjectThemeWork::classname());
            Model::loadMultiple($modelThemes, Yii::$app->request->post());
            if (Model::validateMultiple($modelThemes, ['id', 'name', 'project_type', 'description'])) {
                $formPitch->themes = $modelThemes;
            }

            $modelExperts = Model::createMultiple(TrainingGroupExpertWork::classname());
            Model::loadMultiple($modelExperts, Yii::$app->request->post());
            if (Model::validateMultiple($modelExperts, ['id', 'expertId', 'expert_type'])) {
                $formPitch->experts = $modelExperts;
            }

            $this->service->createNewThemes($formPitch);
            $this->service->attachThemes($formPitch);
            $this->service->attachExperts($formPitch);
            $formPitch->releaseEvents();

            return $this->redirect(['view', 'id' => $formPitch->id]);
        }

        return $this->render('_form-pitch', [
            'model' => $formPitch,
            'peoples' => $peoples
        ]);
    }

    public function actionDeleteLesson($groupId, $entityId)
    {
        /** @var TrainingGroupLessonWork $model */
        $model = $this->groupLessonRepository->get($entityId);
        $result = $this->groupLessonRepository->delete($model);

        if ($result) {
            Yii::$app->session->setFlash('success', 'Занятие успешно удалено');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Ошибка удаления занятия');
        }

        return $this->redirect(['schedule-form', 'id' => $groupId]);
    }

    public function actionView($id)
    {
        $form = new TrainingGroupCombinedForm($id);

        return $this->render('view', [
            'model' => $form,
        ]);
    }

    public function actionDelete($id)
    {
        /** @var TrainingGroupWork $model */
        $model = $this->trainingGroupRepository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->trainingGroupRepository->delete($model);
            Yii::$app->session->addFlash('success', 'Группа "'.$model->number.'" успешно удалена');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionGroupDeletion($id)
    {
        $data = RequestHelper::getDataFromPost(Yii::$app->request->post(), 'check', RequestHelper::CHECKBOX);
        foreach ($data as $item) {
            /** @var TrainingGroupLessonWork $entity */
            $entity = $this->groupLessonRepository->get($item);
            $this->groupLessonRepository->delete($entity);
        }

        $formData = $this->service->prepareFormScheduleData($id);
        $formSchedule = $formData['formSchedule'];
        $modelLessons = $formData['modelLessons'];
        $auditoriums = $formData['auditoriums'];
        $scheduleTable = $formData['scheduleTable'];

        return $this->render('_form-schedule', [
            'model' => $formSchedule,
            'modelLessons' => count($modelLessons) > 0 ? $modelLessons : [new TrainingGroupParticipantWork],
            'auditoriums' => $auditoriums,
            'scheduleTable' => $scheduleTable
        ]);
    }
}