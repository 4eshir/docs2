<?php

namespace console\controllers;

use common\models\LoginForm;
use common\models\work\rac\UserPermissionFunctionWork;
use common\repositories\general\UserRepository;
use common\repositories\rac\UserPermissionFunctionRepository;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\console\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class AccessModelController extends Controller
{
    private UserPermissionFunctionRepository $userFunctionRepository;

    public function __construct(
        $id,
        $module,
        UserPermissionFunctionRepository $userFunctionRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userFunctionRepository = $userFunctionRepository;
    }

    //-----------------------------------------------

    public function actionAttachTemplate()
    {
        $template = $this->prompt(
            'Введите название шаблона (teacher, study_info, event_info, doc_info, material_info, branch_controller, super_controller, admin):'
        );

        $userId = $this->prompt('Введите ID пользователя:');
        $branch = $this->prompt('Введите отдел для правила (techno, quant, cdntt, cod, mob_quant) или оставьте пустым:');

        $hasException = false;
        try {
            $this->userFunctionRepository->attachTemplatePermissionsToUser($template, $userId, $branch);
        }
        catch (Exception $e) {
            $hasException = true;
            echo "Возникла ошибка во время назначения прав пользователю";
        }

        if (!$hasException) {
            echo "Права успешно назначены пользователю";
        }
    }
}
