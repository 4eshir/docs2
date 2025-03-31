<?php

namespace backend\controllers;

use common\models\Error;
use common\models\work\ErrorsWork;
use common\repositories\general\ErrorsRepository;
use common\repositories\rubac\PermissionTokenRepository;
use frontend\models\work\rubac\PermissionTokenWork;
use Yii;
use yii\web\Controller;

class DaemonController extends Controller
{
    private ErrorsRepository $errorsRepository;
    private PermissionTokenRepository $tokenRepository;

    public function __construct(
        $id,
        $module,
        ErrorsRepository $errorsRepository,
        PermissionTokenRepository $tokenRepository,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->errorsRepository = $errorsRepository;
        $this->tokenRepository = $tokenRepository;
    }

    // Эндпоинт обновления статусов ошибок для демона
    public function actionChangeErrorsState()
    {
        /** @var ErrorsWork[] $errors */
        $errors = $this->errorsRepository->getChangeableErrors();

        foreach ($errors as $error) {
            /** @var Error $errorEntity */
            $errorEntity = Yii::$app->errors->get($error->error);
            $errorEntity->changeState($error->id);
        }
    }

    // Эндпоинт очистки протухших токенов (временных разрешений)
    public function actionRemoveTokenPermissions()
    {
        /** @var PermissionTokenWork[] $tokens */
        $tokens = $this->tokenRepository->getAll();

        foreach ($tokens as $token) {
            if (strtotime('now') > strtotime($token->end_time)) {
                $this->tokenRepository->delete($token);
            }
        }
    }
}