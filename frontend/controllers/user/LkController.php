<?php

namespace frontend\controllers\user;

use common\repositories\general\UserRepository;
use DomainException;
use frontend\forms\ChangePasswordForm;
use frontend\models\work\general\UserWork;
use Yii;
use yii\web\Controller;

class LkController extends Controller
{
    private UserRepository $userRepository;

    public function __construct($id, $module, UserRepository $userRepository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userRepository = $userRepository;
    }

    public function actionInfo(int $id)
    {
        $model = $this->userRepository->get($id);

        return $this->render('info', [
            'model' => $model,
        ]);
    }

    public function actionChangePassword(int $id)
    {
        $model = new ChangePasswordForm();
        /** @var UserWork $user */
        $user = $this->userRepository->get($id);

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }

            if (!$user->validatePassword($model->oldPass)) {
                Yii::$app->session->setFlash('danger', 'Указан некорректный текущий пароль');
            }
            else {
                $this->userRepository->changePassword($model->newPass, $id);
                Yii::$app->session->setFlash('success', 'Пароль успешно изменен');

                return $this->render('info', [
                    'model' => $user,
                ]);
            }
        }

        return $this->render('change-password', [
            'model' => $model,
            'user' => $user,
        ]);
    }
}