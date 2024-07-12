<?php

namespace common\services\access;

use common\repositories\rac\UserPermissionFunctionRepository;
use Yii;

class RacService
{
    private UserPermissionFunctionRepository $userPermissionFunctionRepository;
    private $permissions = [];

    public function __construct(UserPermissionFunctionRepository $userPermissionFunctionRepository)
    {
        $this->userPermissionFunctionRepository = $userPermissionFunctionRepository;
    }

    public function init()
    {
        if (Yii::$app->user->identity->getId()) {
            $this->permissions = $this->userPermissionFunctionRepository->getPermissionsByUser(Yii::$app->user->identity->getId());
            return true;
        }

        return false;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }
}