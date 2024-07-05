<?php

namespace common\repositories\rac;

use common\models\work\rac\PermissionTemplateWork;
use common\models\work\rac\UserPermissionFunctionWork;
use DomainException;
use Yii;
use yii\web\NotFoundHttpException;

class UserPermissionFunctionRepository
{
    private PermissionFunctionRepository $permissionFunctionRepository;

    public function __construct(PermissionFunctionRepository $permissionFunctionRepository)
    {
        $this->permissionFunctionRepository = $permissionFunctionRepository;
    }

    public function attachTemplatePermissionsToUser($templateName, $userId, $branch)
    {
        if (array_key_exists($templateName, PermissionTemplateWork::getTemplateNames()) &&
            (array_key_exists($branch, Yii::$app->branches->getList()) || $branch == null)) {
            $functions = $this->permissionFunctionRepository->getTemplateLinkedPermissions($templateName);

            foreach ($functions as $function) {
                $this->save(
                    UserPermissionFunctionWork::fill(
                        $userId,
                        $function->id,
                        $branch
                    )
                );
            }

            return true;
        }

        throw new NotFoundHttpException("Неизвестный тип шаблона - $templateName или неизвестный отдел - $branch");
    }

    public function save(UserPermissionFunctionWork $userFunction)
    {
        if (!$userFunction->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($userFunction->getErrors()));
        }

        return $userFunction->id;
    }
}