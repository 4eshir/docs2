<?php

namespace frontend\controllers;

use common\repositories\general\UserRepository;
use frontend\models\auth\LoginModel;
use frontend\models\work\general\UserWork;
use Yii;
use yii\web\Controller;

class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct($id, $module, UserRepository $userRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
    }

    public function actionLogin()
    {
        if (!Yii::$app->rac->isGuest()) {
            return $this->redirect(['site/index']);
        }

        $model = new LoginModel();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $this->userRepository->findByUsername($model->username);
            /** @var UserWork $user */
            if ($user && $user->validatePassword($model->password)) {
                $duration = $model->rememberMe ? 3600 * 24 * 365 : 3600 * 12;
                Yii::$app->user->login($user, $duration);
                return $this->redirect(['site/index']);
            }

            Yii::$app->session->setFlash('danger', 'Неверное имя пользователя и/или пароль');
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }
}