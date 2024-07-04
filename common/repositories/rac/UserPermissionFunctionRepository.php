<?php

namespace common\repositories\rac;

use common\models\work\rac\PermissionTemplateWork;
use common\models\work\rac\UserPermissionFunctionWork;
use DomainException;

class UserPermissionFunctionRepository
{
    private PermissionFunctionRepository $permissionFunctionRepository;

    public function __construct(PermissionFunctionRepository $permissionFunctionRepository)
    {
        $this->permissionFunctionRepository = $permissionFunctionRepository;
    }

    public function attachTemplatePermissionsToUser($templateName, $userId, $branch)
    {
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
    }

    public function save(UserPermissionFunctionWork $userFunction)
    {
        if (!$userFunction->save()) {
            throw new DomainException('Ошибка привязки правила к пользователю. Проблемы: '.json_encode($userFunction->getErrors()));
        }

        return $userFunction->id;
    }
}