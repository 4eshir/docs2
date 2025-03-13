<?php

namespace frontend\controllers\educational;

use common\components\traits\AccessControl;
use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\ButtonsFormatter;
use common\helpers\common\RequestHelper;
use common\helpers\html\HtmlBuilder;
use common\Model;
use common\repositories\dictionaries\AuditoriumRepository;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\educational\TrainingGroupLessonRepository;
use common\repositories\educational\TrainingGroupRepository;
use common\repositories\educational\TrainingProgramRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\visit\DeleteLessonFromVisitEvent;
use frontend\forms\training_group\PitchGroupForm;
use frontend\forms\training_group\TrainingGroupBaseForm;
use frontend\forms\training_group\TrainingGroupCombinedForm;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\models\search\SearchTrainingGroup;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\ProjectThemeWork;
use frontend\services\educational\JournalService;
use frontend\services\educational\TrainingGroupService;
use Yii;

class TrainingGroupController extends DocumentController
{
    use AccessControl;

    private TrainingGroupService $service;
    private JournalService $journalService;
    private TrainingProgramRepository $trainingProgramRepository;
    private TrainingGroupRepository $trainingGroupRepository;
    private TrainingGroupLessonRepository $groupLessonRepository;
    private ForeignEventParticipantsRepository $participantsRepository;
    private PeopleRepository $peopleRepository;
    private AuditoriumRepository $auditoriumRepository;
    private LockWizard $lockWizard;

    public function __construct(
        $id,
        $module,
        FileService $fileService,
        FilesRepository $filesRepository,
        TrainingGroupService $service,
        JournalService $journalService,
        TrainingProgramRepository $trainingProgramRepository,
        TrainingGroupRepository $trainingGroupRepository,
        TrainingGroupLessonRepository $groupLessonRepository,
        ForeignEventParticipantsRepository $participantsRepository,
        PeopleRepository $peopleRepository,
        AuditoriumRepository $auditoriumRepository,
        LockWizard $lockWizard,
        $config = [])
    {
        parent::__construct($id, $module, $fileService, $filesRepository, $config);
        $this->service = $service;
        $this->journalService = $journalService;
        $this->trainingProgramRepository = $trainingProgramRepository;
        $this->trainingGroupRepository = $trainingGroupRepository;
        $this->groupLessonRepository = $groupLessonRepository;
        $this->participantsRepository = $participantsRepository;
        $this->peopleRepository = $peopleRepository;
        $this->auditoriumRepository = $auditoriumRepository;
        $this->lockWizard = $lockWizard;
    }


    public function actionIndex()
    {
        $searchModel = new SearchTrainingGroup();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $links = array_merge(
            ButtonsFormatter::anyOneLink('Добавить программу', 'create', 'btn-primary'),
            ButtonsFormatter::anyOneLink('Изменить актуальность', Yii::$app->frontUrls::TRAINING_GROUP_RELEVANCE, ButtonsFormatter::BTN_SUCCESS)
        );
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'buttonsAct' => $buttonHtml,
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
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            $formBase = new TrainingGroupBaseForm($id);
            $programs = $this->trainingProgramRepository->getAll();
            $people = $this->peopleRepository->getPeopleFromMainCompany();
            $tables = $this->service->getUploadedFilesTables($formBase);

            if ($formBase->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
                if (!$formBase->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($formBase->getErrors()));
                }
                $groupModel = $this->service->convertBaseFormToModel($formBase);

                $modelTeachers = Model::createMultiple(TeacherGroupWork::classname());
                Model::loadMultiple($modelTeachers, Yii::$app->request->post());
                if (Model::validateMultiple($modelTeachers, ['peopleId'])) {
                    $formBase->teachers = $modelTeachers;
                    $groupModel->generateNumber($this->peopleRepository->get($formBase->teachers[0]->peopleId));
                } else {
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
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionParticipantForm($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            $formParticipant = new TrainingGroupParticipantForm($id);
            $childs = $this->participantsRepository->getSortedList(ForeignEventParticipantsRepository::SORT_FIO);

            if (count(Yii::$app->request->post()) > 0) {
                $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
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
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionScheduleForm($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            $formData = $this->service->prepareFormScheduleData($id);
            $formSchedule = $formData['formSchedule'];
            $modelLessons = $formData['modelLessons'];
            $auditoriums = $formData['auditoriums'];
            $scheduleTable = $formData['scheduleTable'];

            if ($formSchedule->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
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
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionPitchForm($id)
    {
        if ($this->lockWizard->lockObject($id, TrainingGroupWork::tableName(), Yii::$app->user->id)) {
            $formPitch = new PitchGroupForm($id);
            $peoples = $this->peopleRepository->getPeopleFromMainCompany();

            if ($formPitch->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, TrainingGroupWork::tableName());
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
        else {
            Yii::$app->session->setFlash
            ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, TrainingGroupWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionCreateLessonThemes($groupId)
    {
        $result = $this->service->createLessonThemes($groupId);
        if ($result === TrainingGroupWork::ERROR_NO_PROGRAM) {
            Yii::$app->session->setFlash('danger', 'Ошибка создания тематического плана: у группы отсутствует образовательная программа');
        }
        if ($result === TrainingGroupWork::ERROR_THEMES_MISMATCH) {
            Yii::$app->session->setFlash('danger', 'Ошибка создания тематического плана: количество занятий группы не совпадает с количеством тем в образовательной программе');
        }

        if ($result === true) {
            Yii::$app->session->setFlash('success', 'Тематический план создан');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdateLesson($groupId, $entityId)
    {
        /** @var TrainingGroupLessonWork $model */
        $model = $this->groupLessonRepository->get($entityId);
        $auditoriums = $this->auditoriumRepository->getByBranch($model->branch);

        if ($model->load(Yii::$app->request->post())) {
            $this->groupLessonRepository->save($model);

            return $this->redirect(['schedule-form', 'id' => $groupId]);
        }

        return $this->render('update-lesson', [
            'model' => $model,
            'auds' => $auditoriums
        ]);
    }

    public function actionDeleteLesson($groupId, $entityId)
    {
        /** @var TrainingGroupLessonWork $model */
        $model = $this->groupLessonRepository->get($entityId);
        $model->recordEvent(
            new DeleteLessonFromVisitEvent($groupId, [$model]),
            TrainingGroupLessonWork::class
        );
        $result = $this->groupLessonRepository->delete($model);

        if ($result) {
            Yii::$app->session->setFlash('success', 'Занятие успешно удалено');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Ошибка удаления занятия');
        }

        $model->releaseEvents();
        return $this->redirect(['schedule-form', 'id' => $groupId]);
    }

    public function actionView($id)
    {
        $form = new TrainingGroupCombinedForm($id);

        return $this->render('view', [
            'model' => $form,
            'journalState' => $this->journalService->checkJournalStatus($id)
        ]);
    }

    public function actionGenerateJournal($id)
    {
        $result = $this->journalService->createJournal($id);
        if ($result) {
            Yii::$app->session->setFlash('success', 'Журнал успешно создан');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Ошибка создания журнала');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDeleteJournal($id)
    {
        $result = $this->journalService->deleteJournal($id);
        if ($result) {
            Yii::$app->session->setFlash('success', 'Журнал успешно удален');
        }
        else {
            Yii::$app->session->setFlash('danger', 'Ошибка удаления журнала');
        }
        return $this->redirect(['view', 'id' => $id]);
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
            $entity->recordEvent(
                new DeleteLessonFromVisitEvent($id, [$entity]),
                TrainingGroupLessonWork::class
            );
            $this->groupLessonRepository->delete($entity);
            $entity->releaseEvents();
        }

        return $this->redirect(['schedule-form', 'id' => $id]);
    }

    public function actionArchive()
    {

    }

    public function actionSubAuds()
    {
        $result = HtmlBuilder::createEmptyOption('Вне отдела');
        if ($branch = Yii::$app->request->post('branch')) {
            if (array_key_exists($branch, Yii::$app->branches->getOnlyEducational())) {
                $result .= HtmlBuilder::buildOptionList($this->auditoriumRepository->getByBranch($branch));
            }
        }

        echo $result;
    }

    public function beforeAction($action)
    {
        $result = $this->checkActionAccess($action);
        if ($result['url'] !== '') {
            $this->redirect($result['url']);
            return $result['status'];
        }

        return parent::beforeAction($action);
    }
}