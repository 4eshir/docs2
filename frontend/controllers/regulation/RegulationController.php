<?php

namespace frontend\controllers\regulation;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\SortHelper;
use common\models\search\SearchDocumentIn;
use common\models\search\SearchRegulation;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\general\FilesWork;
use common\models\work\regulation\RegulationWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\general\CompanyRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\PeopleRepository;
use common\repositories\general\PositionRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\document_in\InOutDocumentCreateEvent;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\helpers\HeaderWizard;
use frontend\services\document\DocumentInService;
use frontend\services\regulation\RegulationService;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class RegulationController extends Controller
{
    private RegulationRepository $repository;
    private RegulationService $service;
    private FileService $fileService;
    private FilesRepository $filesRepository;

    public function __construct(
        $id,
        $module,
        RegulationRepository $repository,
        RegulationService $service,
        FileService $fileService,
        FilesRepository $filesRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->service = $service;
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
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

    public function actionUpdate($id)
    {
        $model = $this->repository->get($id);
        /** @var RegulationWork $model */
        $scanFile = $model->getFileLinks(FilesHelper::TYPE_SCAN);;

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
            'scanFile' => $scanFile,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        $number = $model->fullNumber;
        if ($model) {
            $this->repository->delete($model);
            Yii::$app->session->setFlash('success', "Положение $number успешно удалено");
            return $this->redirect(['index']);
        }
        else {
            throw new DomainException('Модель не найдена');
        }
    }

    public function actionGetFile($filepath)
    {
        $data = $this->fileService->downloadFile($filepath);
        if ($data['type'] == FilesHelper::FILE_SERVER) {
            Yii::$app->response->sendFile($data['obj']->file);
        }
        else {
            $fp = fopen('php://output', 'r');
            HeaderWizard::setFileHeaders(FilesHelper::getFilenameFromPath($data['obj']->filepath), $data['obj']->file->size);
            $data['obj']->file->download($fp);
            fseek($fp, 0);
        }
    }

    public function actionDeleteFile($modelId, $fileId)
    {
        try {
            $file = $this->filesRepository->getById($fileId);

            /** @var FilesWork $file */
            $filepath = $file ? basename($file->filepath) : '';
            $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
            $file->recordEvent(new FileDeleteEvent($fileId), get_class($file));
            $file->releaseEvents();

            Yii::$app->session->setFlash('success', "Файл $filepath успешно удален");
            return $this->redirect(['update', 'id' => $modelId]);
        }
        catch (DomainException $e) {
            return $e->getMessage();
        }
    }


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