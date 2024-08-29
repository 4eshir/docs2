<?php

namespace frontend\controllers\document;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\SortHelper;
use common\models\search\SearchDocumentIn;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\general\FilesWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\general\CompanyRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\PeopleRepository;
use common\repositories\general\PositionRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\document_in\InOutDocumentCreateEvent;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\helpers\HeaderWizard;
use frontend\services\document\DocumentInService;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class DocumentInController extends Controller
{
    private DocumentInRepository $repository;
    private PeopleRepository $peopleRepository;
    private PositionRepository $positionRepository;
    private CompanyRepository $companyRepository;
    private FileService $fileService;
    private FilesRepository $filesRepository;
    private DocumentInService $service;

    public function __construct(
        $id,
        $module,
        DocumentInRepository $repository,
        PeopleRepository $peopleRepository,
        PositionRepository $positionRepository,
        CompanyRepository $companyRepository,
        FileService $fileService,
        FilesRepository $filesRepository,
        DocumentInService $service,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->peopleRepository = $peopleRepository;
        $this->positionRepository = $positionRepository;
        $this->companyRepository = $companyRepository;
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
        $this->service = $service;
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

            $this->service->getFilesInstances($model);
            $model->need_answer = $this->repository->setAnswer($model);
            $this->repository->save($model);

            if ($model->needAnswer) {
                $model->recordEvent(new InOutDocumentCreateEvent($model->id, null, $model->dateAnswer, $model->nameAnswer), DocumentInWork::class);
            }

            $this->service->saveFilesFromModel($model);
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
    public function actionReserve()
    {
        $model = new DocumentInWork();
        $this->repository->createReserve($model);
        $model->generateDocumentNumber();
        $this->repository->save($model);
        return $this->redirect(['index']);
    }

    public function actionUpdate($id)
    {
        $model = $this->repository->get($id);

        /** @var DocumentInWork $model */
        $model->setNeedAnswer();

        $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
        $availablePositions = $this->positionRepository->getList($model->correspondent_id);
        $availableCompanies = $this->companyRepository->getList($model->correspondent_id);
        $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();
        $scanFile = $this->filesRepository->get($model::tableName(), $model->id, FilesHelper::TYPE_SCAN);
        $docFiles = $this->filesRepository->get($model::tableName(), $model->id, FilesHelper::TYPE_DOC);
        $appFiles = $this->filesRepository->get($model::tableName(), $model->id, FilesHelper::TYPE_APP);
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->service->getFilesInstances($model);
            $model->need_answer = $this->repository->setAnswer($model);
            $this->repository->save($model);

            if ($model->needAnswer) {

                $model->recordEvent(
                    new InOutDocumentCreateEvent(
                        $model->id,
                        null,
                        DateFormatter::format($model->dateAnswer, DateFormatter::dmY_dot, DateFormatter::Ymd_dash),
                        $model->nameAnswer
                    ),
                    DocumentInWork::class
                );
            }
            else {
                $model->recordEvent(new InOutDocumentDeleteEvent($model->id), DocumentInWork::class);
            }

            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
            'scanFile' => $scanFile,
            'docFiles' => $docFiles,
            'appFiles' => $appFiles,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        $number = $model->fullNumber;
        if ($model) {
            $this->repository->delete($model);
            Yii::$app->session->setFlash('success', "Документ $number успешно удален");
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
            $this->fileService->deleteFile($file->createAdditionalPath() . $file->filepath);
            $file->recordEvent(new FileDeleteEvent($fileId), get_class($file));
            $file->releaseEvents();

            Yii::$app->session->setFlash('success', "Файл $filepath успешно удален");
            return $this->redirect(['update', 'id' => $modelId]);
        }
        catch (DomainException $e) {
            return $e->getMessage();
        }
    }

    public function actionDependencyDropdown()
    {
        $id = Yii::$app->request->post('id');
        $response = '';

        if ($id === '') {
            // Получаем позиции и компании
            $response .= HtmlBuilder::buildOptionList($this->positionRepository->getList());
            $response .= "|split|";
            $response .= HtmlBuilder::buildOptionList($this->companyRepository->getList());
        } else {
            // Получаем позиции для указанного ID
            $positions = $this->positionRepository->getList($id);
            $response .= count($positions) > 0 ? HtmlBuilder::buildOptionList($positions) : HtmlBuilder::createEmptyOption();
            $response .= "|split|";
            // Получаем компанию для указанного ID
            $companies = $this->companyRepository->getList($id);
            $response .= count($companies) > 0 ? HtmlBuilder::buildOptionList($companies) : HtmlBuilder::createEmptyOption();
        }

        echo $response;
    }

    public function beforeAction($action)
    {
        /*if (Yii::$app->rac->isGuest() || !Yii::$app->rac->checkUserAccess(Yii::$app->rac->authId(), get_class(Yii::$app->controller), $action)) {
            //Yii::$app->session->setFlash('error', 'У Вас недостаточно прав. Обратитесь к администратору для получения доступа');
            $this->redirect(Yii::$app->request->referrer);
            return false;
        }*/

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}