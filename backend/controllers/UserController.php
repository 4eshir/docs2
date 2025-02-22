<?php

namespace backend\controllers;

use backend\models\forms\UserForm;
use backend\models\search\SearchUser;
use common\models\work\UserWork;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\UserRepository;
use common\repositories\rubac\PermissionFunctionRepository;
use common\repositories\rubac\UserPermissionFunctionRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class UserController extends Controller
{
    private UserRepository $repository;
    private PeopleRepository $peopleRepository;
    private PermissionFunctionRepository $permissionRepository;
    private UserPermissionFunctionRepository $userPermissionRepository;

    public function __construct(
        $id,
        $module,
        UserRepository $repository,
        PeopleRepository $peopleRepository,
        PermissionFunctionRepository $permissionRepository,
        UserPermissionFunctionRepository $userPermissionRepository,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->peopleRepository = $peopleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->userPermissionRepository = $userPermissionRepository;
    }

    public function actionIndex()
    {
        $searchModel = new SearchUser();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new UserForm(
            new UserWork(),
            $this->peopleRepository->getAll(),
            [],
            $this->permissionRepository->getAllPermissions()
        );

        if ($model->load(Yii::$app->request->post())) {
            $this->repository->save($model->entity);
            $model->savePermissions();
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->entity->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = new UserForm(
            $this->repository->get($id),
            $this->peopleRepository->getAll(),
            $this->userPermissionRepository->getPermissionsByUser($id),
            $this->permissionRepository->getAllPermissions()
        );

        if ($model->load(Yii::$app->request->post())) {
            $this->repository->save($model->entity);
            $model->savePermissions();
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->entity->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model = new UserForm(
            $this->repository->get($id),
            $this->peopleRepository->getAll(),
            $this->userPermissionRepository->getPermissionsByUser($id),
            $this->permissionRepository->getAllPermissions()
        );

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален пользователь '.$this->findModel($id)->username);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteRole($roleId, $modelId)
    {
        $role = UserRoleWork::find()->where(['id' => $roleId])->one();
        $name = $role->role->name;
        $role->delete();
        $user = UserWork::find()->where(['id' => $modelId])->one();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Откреплена роль ' . $name . ' от пользователя '. $user->secondname . ' ' . $user->firstname);

        return $this->redirect('index?r=user/update&id='.$modelId);
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function beforeAction($action)
    {
        if (Yii::$app->rubac->isGuest() || !Yii::$app->rubac->checkUserAccess(Yii::$app->rubac->authId(), get_class(Yii::$app->controller), $action)) {
            Yii::$app->session->setFlash('error', 'У Вас недостаточно прав. Обратитесь к администратору для получения доступа');
            $this->redirect(Yii::$app->request->referrer);
            return false;
        }

        return parent::beforeAction($action);
    }
}