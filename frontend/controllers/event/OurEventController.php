<?php

namespace frontend\controllers\event;

use common\helpers\SortHelper;
use common\models\search\SearchEvent;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\event\EventRepository;
use common\repositories\regulation\RegulationRepository;
use frontend\models\work\event\EventWork;
use frontend\services\event\EventService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * EventController implements the CRUD actions for Event model.
 */
class OurEventController extends Controller
{
    private EventRepository $repository;
    private EventService $service;
    private PeopleRepository $peopleRepository;
    private RegulationRepository $regulationRepository;

    public function __construct(
                             $id,
                             $module,
        EventRepository      $repository,
        EventService         $service,
        PeopleRepository     $peopleRepository,
        RegulationRepository $regulationRepository,
                             $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->service = $service;
        $this->peopleRepository = $peopleRepository;
        $this->regulationRepository = $regulationRepository;
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
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        /*
         * Тут вроде как нужен PBAC для проверки отдела
         * if (array_key_exists("SearchEvent", Yii::$app->request->queryParams))
        {
            if (Yii::$app->request->queryParams["SearchEvent"]["eventBranchs"] != null) {
                $searchModel->eventBranchs = Yii::$app->request->queryParams["SearchEvent"]["eventBranchs"];
            }
        }*/

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->repository->get($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventWork();
        //$modelEventsLinks = [new EventsLinkWork];
        //$modelGroups = [new EventTrainingGroupWork];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->service->getFilesInstances($model);

            $model->save();
            $this->service->saveFilesFromModel($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'people' => $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO, SORT_ASC),
            'regulations' => $this->regulationRepository->getOrderedList(),
            'branches' => ArrayHelper::getColumn($this->repository->getBranches($model->id), 'branch'),
        ]);
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelEventsLinks = [new EventsLinkWork];
        $modelGroups = [new EventTrainingGroupWork];

        $eventP = EventParticipantsWork::find()->where(['event_id' => $model->id])->one();
        $model->childs = $eventP->child_participants;
        $model->childs_rst = $eventP->child_rst_participants;
        $model->teachers = $eventP->teacher_participants;
        $model->others = $eventP->other_participants;
        $model->leftAge = $eventP->age_left_border;
        $model->rightAge = $eventP->age_right_border;
        $model->old_name = $model->name;

        

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate(false))
            {
                $model->protocolFile = UploadedFile::getInstances($model, 'protocolFile');
                $model->reportingFile = UploadedFile::getInstances($model, 'reportingFile');
                $model->photoFiles = UploadedFile::getInstances($model, 'photoFiles');
                $model->otherFiles = UploadedFile::getInstances($model, 'otherFiles');

                $modelEventsLinks = DynamicModel::createMultiple(EventsLinkWork::classname());
                DynamicModel::loadMultiple($modelEventsLinks, Yii::$app->request->post());
                $model->eventsLink = $modelEventsLinks;
                $modelGroups = DynamicModel::createMultiple(EventTrainingGroupWork::classname());
                DynamicModel::loadMultiple($modelGroups, Yii::$app->request->post());
                $model->groups = $modelGroups;

                if ($model->validate(false))
                {
                    if ($model->protocolFile !== null)
                        $model->uploadProtocolFile(10);
                    if ($model->reportingFile !== null)
                        $model->uploadReportingFile(10);
                    if ($model->photoFiles !== null)
                        $model->uploadPhotosFiles(10);
                    if ($model->otherFiles !== null)
                        $model->uploadOtherFiles(10);
                    $model->save(false);
                    Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменено мероприятие '.$model->name);
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelEventsLinks' => (empty($modelEventsLinks)) ? [new EventsLinkWork] : $modelEventsLinks,
            'modelGroups' => (empty($modelGroups)) ? [new EventTrainingGroupWork] : $modelGroups,
        ]);
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $eventP = EventParticipantsWork::find()->where(['event_id' => $id])->one();
        if ($eventP != null) $eventP->delete();
        $links = EventsLinkWork::find()->where(['event_id' => $id])->all();
        $name = $this->findModel($id)->name;
        foreach ($links as $link)
            $link->delete();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалено мероприятие '.$name);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteGroup($id, $modelId)
    {
        $group = EventTrainingGroupWork::find()->where(['id' => $id])->one();
        $group->delete();
        return $this->redirect('index?r=event/update&id='.$modelId);
    }

    public function actionDeleteExternalEvent($id, $modelId)
    {
        $eventsLink = EventsLinkWork::find()->where(['id' => $id])->one();
        $eventsLink->delete();
        return $this->redirect('index?r=event/update&id='.$modelId);
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = EventWork::find()->where(['id' => $modelId])->one();

        $eventP = EventParticipantsWork::find()->where(['event_id' => $model->id])->one();
        $model->childs = $eventP->child_participants;
        $model->childs_rst = $eventP->child_rst_participants;
        $model->teachers = $eventP->teacher_participants;
        $model->others = $eventP->other_participants;
        $model->leftAge = $eventP->age_left_border;
        $model->rightAge = $eventP->age_right_border;
        $model->old_name = $model->name;

        $branches = EventBranchWork::find()->where(['event_id' => $model->id])->all();
        foreach ($branches as $branch) 
        {
            if ($branch->branch_id == 1) $model->isQuantorium = 1;
            if ($branch->branch_id == 2) $model->isTechnopark = 1;
            if ($branch->branch_id == 3) $model->isCDNTT = 1;
            if ($branch->branch_id == 4) $model->isMobQuant = 1;
            if ($branch->branch_id == 7) $model->isCod = 1;
        }

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null)
        {
            $fileCell = $model->protocol;
            if ($type == 'photos') $fileCell = $model->photos;
            if ($type == 'report') $fileCell = $model->reporting_doc;
            if ($type == 'other') $fileCell = $model->other_files;
            $result = '';
            $split = explode(" ", $fileCell);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++)
            {
                if ($split[$i] !== $fileName)
                {
                    $result = $result.$split[$i].' ';
                }
                else
                    $deleteFile = $split[$i];
            }

            if ($type == null) $model->protocol = $result;
            if ($type == 'photos') $model->photos = $result;
            if ($type == 'report') $model->reporting_doc = $result;
            if ($type == 'other') $model->other_files = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл '.$deleteFile);
        }
        return $this->redirect('index.php?r=event/update&id='.$modelId);
    }

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //-------------------------

    public function actionGetFile($fileName = null, $type = null)
    {

        $filePath = '/upload/files/'.Yii::$app->controller->id;
        $filePath .= $type == null ? '/' : '/'.$type.'/';

        $downloadServ = new FileDownloadServer($filePath, $fileName);
        $downloadYadi = new FileDownloadYandexDisk($filePath, $fileName);

        $downloadServ->LoadFile();
        if (!$downloadServ->success) $downloadYadi->LoadFile();
        else return \Yii::$app->response->sendFile($downloadServ->file);

        if (!$downloadYadi->success) throw new \Exception('File not found');
        else {

            $fp = fopen('php://output', 'r');

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            fseek($fp, 0);
        }
    }

    public function actionAmnesty ($id)
    {
        $errorsAmnesty = new EventErrorsWork();
        $errorsAmnesty->EventAmnesty($id);
        return $this->redirect('index?r=event/view&id='.$id);
    }

    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        /*if (Yii::$app->rac->isGuest() || !Yii::$app->rac->checkUserAccess(Yii::$app->rac->authId(), get_class(Yii::$app->controller), $action)) {
            Yii::$app->session->setFlash('error', 'У Вас недостаточно прав. Обратитесь к администратору для получения доступа');
            $this->redirect(Yii::$app->request->referrer);
            return false;
        }*/

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
