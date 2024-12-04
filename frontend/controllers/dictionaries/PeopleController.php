<?php

namespace frontend\controllers\dictionaries;

use app\components\DynamicWidget;
use app\events\dictionaries\PeopleEventCreate;
use app\events\dictionaries\PeoplePositionCompanyBranchEventCreate;
use common\components\dictionaries\base\BranchDictionary;
use common\repositories\dictionaries\CompanyRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\dictionaries\PositionRepository;
use DomainException;
use frontend\models\search\SearchPeople;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\general\PeopleWork;
use frontend\services\dictionaries\PeopleService;
use Yii;
use yii\web\Controller;

class PeopleController extends Controller
{
    private PeopleRepository $repository;
    private PeopleService $service;
    private CompanyRepository $companyRepository;

    private PositionRepository $positionRepository;
    public function __construct(
                           $id,
                           $module,
        PeopleRepository   $peopleRepository,
        PeopleService      $service,
        CompanyRepository  $companyRepository,
        PositionRepository $positionRepository,
                           $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $peopleRepository;
        $this->service = $service;
        $this->positionRepository = $positionRepository;
        $this->companyRepository = $companyRepository;
    }

    public function actionIndex()
    {
        $searchModel = new SearchPeople();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->repository->get($id);

        $positions = implode('<br>',
            $this->service->createPositionsCompaniesArray(
                $this->repository->getPositionsCompanies($id)
            )
        );

        return $this->render('view', [
            'model' => $model,
            'positions' => $positions,
        ]);
    }

    public function actionCreate()
    {
        $model = new PeopleWork();
        $companies = $this->companyRepository->getList();
        $positions = $this->positionRepository->getList();
        $branches = Yii::$app->branches->getList();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $postPositions = DynamicWidget::getData(basename(PeopleWork::class), 'positions', $post);
            $postCompanies = DynamicWidget::getData(basename(PeopleWork::class), 'companies', $post);
            $postBranches = DynamicWidget::getData(basename(PeopleWork::class), 'branches', $post);
            $peopleId = $this->repository->save($model);
            $this->service->attachPositionCompanyBranch($model, $postPositions, $postCompanies, $postBranches, $peopleId);

            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'companies' => $companies,
            'positions' => $positions,
            'branches' => $branches
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->repository->get($id);
        /** @var PeopleWork $model */
        $modelPeoplePositionBranch = $this->service->getPositionCompanyBranchTable($this->repository, $model->id);
        $companies = $this->companyRepository->getList();
        $positions = $this->positionRepository->getList();
        $branches = Yii::$app->branches->getList();

        $post = Yii::$app->request->post();
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            $postPositions = DynamicWidget::getData(basename(PeopleWork::class), 'positions', $post);
            $postCompanies = DynamicWidget::getData(basename(PeopleWork::class), 'companies', $post);
            $postBranches = DynamicWidget::getData(basename(PeopleWork::class), 'branches', $post);
            $this->repository->save($model);
            $this->service->attachPositionCompanyBranch($model, $postPositions, $postCompanies, $postBranches);

            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
            'companies' => $companies,
            'positions' => $positions,
            'branches' => $branches
        ]);
    }

    public function actionDelete($id)
    {
        /** @var PeopleWork $model */
        $model = $this->repository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            if ($this->repository->delete($model)) {
                Yii::$app->session->addFlash('success', $model->getFIO(PeopleWork::FIO_FULL).' успешно удален');
            }
            else {
                Yii::$app->session->addFlash('error', 'Произошла ошибка при удалении человека');
                Yii::error($model->getErrors());
            }
        }
        else {
            Yii::$app->session->addFlash('error', implode('<br>', $deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionDeletePosition($id, $modelId)
    {
        $this->repository->deletePosition($id);
        return $this->redirect(['update', 'id' => $modelId]);
    }

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