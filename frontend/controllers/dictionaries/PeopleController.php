<?php

namespace frontend\controllers\dictionaries;

use common\helpers\StringFormatter;
use common\models\search\SearchPeople;
use common\repositories\dictionaries\CompanyRepository;
use common\repositories\dictionaries\PeopleRepository;
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

    public function __construct(
        $id,
        $module,
        PeopleRepository $repository,
        PeopleService $service,
        CompanyRepository $companyRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->service = $service;
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

        $positions = StringFormatter::toStringWithBr(
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
        $modelPeoplePositionBranch = [new PeoplePositionCompanyBranchWork()];
        $companies = $this->companyRepository->getList();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $this->repository->save($model);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
            'companies' => $companies,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->repository->get($id);
        /** @var PeopleWork $model */
        $modelPeoplePositionBranch = [new PeoplePositionCompanyBranchWork()];
        $companies = $this->companyRepository->getList();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $this->repository->save($model);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
            'companies' => $companies,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        $deleteErrors = $this->service->isAvailableDelete($id);

        if (count($deleteErrors) == 0) {
            $model->delete();
        }
        else {
            Yii::$app->session->addFlash('error', StringFormatter::toStringWithBr($deleteErrors));
        }

        return $this->redirect(['index']);
    }

    public function actionDeletePosition($id, $modelId)
    {
        /*$position = PeoplePositionBranchWork::find()->where(['id' => $id])->one();
        $position->delete();*/
        return $this->redirect('index?r=people/update&id='.$modelId);
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