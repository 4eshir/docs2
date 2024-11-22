<?php

namespace frontend\controllers\dictionaries;

use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PersonalDataParticipantRepository;
use DomainException;
use frontend\events\foreign_event_participants\PersonalDataParticipantAttachEvent;
use frontend\models\search\SearchForeignEventParticipants;
use frontend\models\work\auxiliary\LoadParticipants;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonalDataParticipantWork;
use frontend\services\dictionaries\ForeignEventParticipantsService;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * ForeignEventParticipantsController implements the CRUD actions for ForeignEventParticipants model.
 */
class ForeignEventParticipantsController extends Controller
{
    private ForeignEventParticipantsRepository $repository;
    private PersonalDataParticipantRepository $personalDataRepository;
    private ForeignEventParticipantsService $service;

    public function __construct(
                                           $id,
                                           $module,
        ForeignEventParticipantsRepository $repository,
        PersonalDataParticipantRepository  $personalDataRepository,
        ForeignEventParticipantsService    $service,
                                           $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->personalDataRepository = $personalDataRepository;
        $this->service = $service;
    }

    public function actionIndex($sort = null)
    {
        $searchModel = new SearchForeignEventParticipants();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sort);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionView($id)
    {
        /** @var ForeignEventParticipantsWork $model */
        $model = $this->repository->get($id);
        $model->fillPersonalDataRestrict($this->personalDataRepository->getPersonalDataRestrict($id));

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new ForeignEventParticipantsWork();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $this->repository->save($model);

            $model->recordEvent(new PersonalDataParticipantAttachEvent($model->id, $model->pd), PersonalDataParticipantWork::class);
            $model->releaseEvents();

            $this->service->checkCorrectOne($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        /** @var ForeignEventParticipantsWork $model */
        $model = $this->repository->get($id);
        $model->fillPersonalDataRestrict($this->personalDataRepository->getPersonalDataRestrict($id));

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $model->recordEvent(new PersonalDataParticipantAttachEvent($model->id, $model->pd), PersonalDataParticipantWork::class);
            $this->repository->save($model);
            $model->releaseEvents();

            $this->service->checkCorrectOne($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ForeignEventParticipants model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /** @var ForeignEventParticipantsWork $model */
        $model = $this->repository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $this->repository->delete($model);
            Yii::$app->session->addFlash('success', 'Участник деятельности "'.$model->getFIO(ForeignEventParticipantsWork::FIO_FULL).'" успешно удален');
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionFileLoad()
    {
        $model = new LoadParticipants();

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $model->save();
            $this->service->checkCorrectAll();
            return $this->redirect(['index']);
        }

        return $this->render('file-load', [
            'model' => $model,
        ]);
    }

    public function actionCheckCorrect()
    {
        $this->service->checkCorrectAll();
        return $this->redirect(['index']);
    }

    public function actionMergeParticipant()
    {
        $model = new MergeParticipantModel();
        $model->edit_model = new ForeignEventParticipantsWork();

        if ($model->load(Yii::$app->request->post()) && $model->edit_model->load(Yii::$app->request->post())) {
            $model->save();
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Объединены обучающиеся id1: '.$model->id1.' и id2: '.$model->id2);
            Yii::$app->session->setFlash('success', 'Объединение произведено успешно!');
            return $this->redirect(['view', 'id' => $model->id1]);
        }

        return $this->render('merge-participant', [
            'model' => $model,
        ]);
    }

    public function actionInfo($id1, $id2)
    {
        $p1 = ForeignEventParticipantsWork::find()->where(['id' => $id1])->one();
        $p2 = ForeignEventParticipantsWork::find()->where(['id' => $id2])->one();
        $result = '<table class="table table-striped table-bordered detail-view" style="width: 91%">';
        $result .= '<tr><td><b>Фамилия</b></td><td id="td-secondname-1" style="width: 45%">'.$p1->secondname.'</td><td><b>Фамилия</b></td><td style="width: 45%">'.$p2->secondname.'</td></tr>';
        $result .= '<tr><td><b>Имя</b></td><td id="td-firstname-1" style="width: 45%">'.$p1->firstname.'</td><td><b>Имя</b></td><td style="width: 45%">'.$p2->firstname.'</td></tr>';
        $result .= '<tr><td><b>Отчество</b></td><td id="td-patronymic-1" style="width: 45%">'.$p1->patronymic.'</td><td><b>Отчество</b></td><td style="width: 45%">'.$p2->patronymic.'</td></tr>';
        $result .= '<tr><td><b>Пол</b></td><td id="td-sex-1" style="width: 45%">'.$p1->sex.'</td><td><b>Пол</b></td><td style="width: 45%">'.$p2->sex.'</td></tr>';
        $result .= '<tr><td><b>Дата рождения</b></td><td id="td-birthdate-1" style="width: 45%">'.$p1->birthdate.'</td><td><b>Дата рождения</b></td><td style="width: 45%">'.$p2->birthdate.'</td></tr>';

        $events = TrainingGroupParticipantWork::find()->where(['participant_id' => $id1])->all();

        $eventsLink1 = '';
        $eventsLink2 = '';
        
        foreach ($events as $event)
        {

            $eventsLink1 .= date('d.m.Y', strtotime($event->trainingGroup->start_date)).' - '.date('d.m.Y', strtotime($event->trainingGroup->finish_date)).' | ';
            $eventsLink1 = $eventsLink1.Html::a('Группа '.$event->trainingGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $event->training_group_id]));

            if ($event->trainingGroup->finish_date < date("Y-m-d"))
                $eventsLink1 .= ' (группа завершила обучение)';
            else
                $eventsLink1 .= ' <div style="background-color: green; display: inline"><font color="white"> (проходит обучение)</font></div>';

            if ($event->status === 2)
                $eventsLink1 .= ' | Переведен';

            if ($event->status === 1)
                $eventsLink1 .= ' | Отчислен';

            $eventsLink1 .= '<br>';
        }

        $events = TrainingGroupParticipantWork::find()->where(['participant_id' => $id2])->all();
        
        foreach ($events as $event)
        {
            $eventsLink2 .= date('d.m.Y', strtotime($event->trainingGroup->start_date)).' - '.date('d.m.Y', strtotime($event->trainingGroup->finish_date)).' | ';
            $eventsLink2 = $eventsLink2.Html::a('Группа '.$event->trainingGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $event->training_group_id]));

            if ($event->trainingGroup->finish_date < date("Y-m-d"))
                $eventsLink2 .= ' (группа завершила обучение)';
            else
                $eventsLink2 .= ' <div style="background-color: green; display: inline"><font color="white"> (проходит обучение)</font></div>';

            if ($event->status === 2)
                $eventsLink2 .= ' | Переведен';

            if ($event->status === 1)
                $eventsLink2 .= ' | Отчислен';

            $eventsLink2 .= '<br>';
        }

        $result .= '<tr><td><b>Группы</b></td><td style="width: 45%">'.$eventsLink1.'</td><td><b>Группы</b></td><td style="width: 45%">'.$eventsLink2.'</td></tr>';


        $events = TeacherParticipantWork::find()->where(['participant_id' => $id1])->all();
        $eventsLink1 = '';
        foreach ($events as $event)
            $eventsLink1 = $eventsLink1.Html::a($event->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $event->foreign_event_id])).'<br>';

        $events = TeacherParticipantWork::find()->where(['participant_id' => $id2])->all();
        $eventsLink2 = '';
        foreach ($events as $event)
            $eventsLink2 = $eventsLink2.Html::a($event->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $event->foreign_event_id])).'<br>';

        $result .= '<tr><td><b>Мепроприятия</b></td><td style="width: 45%">'.$eventsLink1.'</td><td><b>Мепроприятия</b></td><td style="width: 45%">'.$eventsLink2.'</td></tr>';

        $achieves = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.participant_id' => $id1])->all();
        $achievesLink1 = '';
        foreach ($achieves as $achieveOne)
        {
            $achievesLink1 = $achievesLink1.$achieveOne->achievment.' &mdash; '.Html::a($achieveOne->teacherParticipantWork->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $achieveOne->teacherParticipantWork->foreign_event_id])).
                ' ('.$achieveOne->teacherParticipantWork->foreignEvent->start_date.')'.'<br>';
        }

        $achieves = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.participant_id' => $id2])->all();
        $achievesLink2 = '';
        foreach ($achieves as $achieveOne)
        {
            $achievesLink2 = $achievesLink2.$achieveOne->achievment.' &mdash; '.Html::a($achieveOne->teacherParticipantWork->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $achieveOne->teacherParticipantWork->foreign_event_id])).
                ' ('.$achieveOne->teacherParticipantWork->foreignEvent->start_date.')'.'<br>';
        }

        $result .= '<tr><td><b>Достижения</b></td><td style="width: 45%">'.$achievesLink1.'</td><td><b>Достижения</b></td><td style="width: 45%">'.$achievesLink2.'</td></tr>';

        $resultN = "<table class='table table-bordered'>";
        $pds = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $id1])->orderBy(['id' => SORT_ASC])->all();
        foreach ($pds as $pd)
        {
            $resultN .= '<tr><td style="width: 350px">';
            if ($pd->status == 0) $resultN .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-success b1">Разрешено</span></td>';
            else $resultN .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-error b1">Запрещено</span></td>';
            $resultN .= '</td></tr>';
        }
        $resultN .= "</table>";

        $resultN1 = "<table class='table table-bordered'>";
        $pds = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $id2])->orderBy(['id' => SORT_ASC])->all();
        foreach ($pds as $pd)
        {
            $resultN1 .= '<tr><td style="width: 350px">';
            if ($pd->status == 0) $resultN1 .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-success">Разрешено</span></td>';
            else $resultN1 .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-error">Запрещено</span></td>';
            $resultN1 .= '</td></tr>';
        }
        $resultN1 .= "</table>";

        $result .= '<tr><td><b>Разглашение ПД</b></td><td style="width: 45%">'.$resultN.'</td><td><b>Разглашение ПД</b></td><td style="width: 45%">'.$resultN1.'</td></tr>';

        $result .= '</table><br>';
        $result .= '<a id="fill1" style="display: block; width: 91%" onclick="FillEditForm()" class="btn btn-primary">Открыть форму редактирования</a>';

        return $result;
    }

    /**
     * Finds the ForeignEventParticipants model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ForeignEventParticipantsWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ForeignEventParticipantsWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
