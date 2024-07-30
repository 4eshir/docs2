<?php

namespace frontend\controllers\document;

use Cassandra\Exception\ValidationException;
use common\helpers\files\filenames\DocumentInFileNameGenerator;
use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\helpers\SortHelper;
use common\models\search\SearchDocumentIn;
use common\models\work\document_in_out\DocumentInWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\general\CompanyRepository;
use common\repositories\general\PeopleRepository;
use common\repositories\general\PositionRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\document_in\InOutDocumentCreateEvent;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

class DocumentInController extends Controller
{
    private DocumentInRepository $repository;
    private PeopleRepository $peopleRepository;
    private PositionRepository $positionRepository;
    private CompanyRepository $companyRepository;
    private FileService $fileService;

    public function __construct(
        $id,
        $module,
        DocumentInRepository $repository,
        PeopleRepository $peopleRepository,
        PositionRepository $positionRepository,
        CompanyRepository $companyRepository,
        FileService $fileService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->peopleRepository = $peopleRepository;
        $this->positionRepository = $positionRepository;
        $this->companyRepository = $companyRepository;
        $this->fileService = $fileService;
    }

    public function actionIndex()
    {
        $searchModel = new SearchDocumentIn();
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
        $model = new DocumentInWork();
        $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
        $availablePositions = $this->positionRepository->getList();
        $availableCompanies = $this->companyRepository->getList();
        $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();

        if ($model->load(Yii::$app->request->post())) {

            $model->generateDocumentNumber();

            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');

            $this->repository->save($model);

            if ($model->needAnswer) {
                $model->recordEvent(new InOutDocumentCreateEvent($model->id, null, $model->dateAnswer, $model->nameAnswer), DocumentInWork::class);
            }

            $this->fileService->uploadFile(
                $model,
                $model->scanFile,
                FilesHelper::TYPE_SCAN,
                FilesHelper::LOAD_TYPE_SINGLE,
                FilePaths::DOCUMENT_IN_SCAN
            );

            for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
                $this->fileService->uploadFile(
                    $model,
                    $model->docFiles[$i - 1],
                    FilesHelper::TYPE_DOC,
                    FilesHelper::LOAD_TYPE_MULTI,
                    FilePaths::DOCUMENT_IN_DOC,
                    ['counter' => $i]
                );
            }

            for ($i = 1; $i < count($model->appFiles) + 1; $i++) {
                $this->fileService->uploadFile(
                    $model,
                    $model->appFiles[$i - 1],
                    FilesHelper::TYPE_APP,
                    FilesHelper::LOAD_TYPE_MULTI,
                    FilePaths::DOCUMENT_IN_APP,
                    ['counter' => $i]
                );
            }

            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->repository->get($id);

        /** @var DocumentInWork $model */
        $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
        $availablePositions = $this->positionRepository->getList();
        $availableCompanies = $this->companyRepository->getList();
        $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');

            $this->repository->save($model);

            if ($model->needAnswer) {
                $model->recordEvent(new InOutDocumentCreateEvent($model->id, null, $model->dateAnswer, $model->nameAnswer), DocumentInWork::class);
            }
            else {
                $model->recordEvent(new InOutDocumentDeleteEvent($model->id), DocumentInWork::class);
            }

            $this->fileService->uploadFile(
                $model,
                $model->scanFile,
                FilesHelper::TYPE_SCAN,
                FilesHelper::LOAD_TYPE_SINGLE,
                FilePaths::DOCUMENT_IN_SCAN
            );

            for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
                $this->fileService->uploadFile(
                    $model,
                    $model->docFiles[$i - 1],
                    FilesHelper::TYPE_DOC,
                    FilesHelper::LOAD_TYPE_MULTI,
                    FilePaths::DOCUMENT_IN_DOC,
                    ['counter' => $i]
                );
            }

            for ($i = 1; $i < count($model->appFiles) + 1; $i++) {
                $this->fileService->uploadFile(
                    $model,
                    $model->appFiles[$i - 1],
                    FilesHelper::TYPE_APP,
                    FilesHelper::LOAD_TYPE_MULTI,
                    FilePaths::DOCUMENT_IN_APP,
                    ['counter' => $i]
                );
            }

            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
        ]);
    }

    public function actionGetFile($filepath)
    {
        $data = $this->fileService->downloadFile($filepath);
        if ($data['type'] == FilesHelper::FILE_SERVER) {
            Yii::$app->response->sendFile($data['obj']->file);
        }
        else {
            $fp = fopen('php://output', 'r');
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . FilesHelper::getFilenameFromPath($data['obj']->filepath));
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $data['obj']->file->size);

            $data['obj']->file->download($fp);

            fseek($fp, 0);
        }
    }
}