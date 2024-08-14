<?php

namespace common\components\access;

use common\models\work\rac\PermissionFunctionWork;
use common\repositories\general\UserRepository;
use common\repositories\rac\UserPermissionFunctionRepository;
use Yii;

class RacComponent
{
    private UserPermissionFunctionRepository $userPermissionFunctionRepository;
    private UserRepository $userRepository;
    private RulesConfig $racConfig;
    private $permissions = [];

    public function __construct(
        UserPermissionFunctionRepository $userPermissionFunctionRepository,
        RulesConfig $racConfig,
        UserRepository $userRepository
    )
    {
        $this->userPermissionFunctionRepository = $userPermissionFunctionRepository;
        $this->racConfig = $racConfig;
        $this->userRepository = $userRepository;
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

    /**
     * Проверка доступа к экшну для конкретного пользователя
     * @param $userId
     * @param $controller
     * @param $action
     * @return bool
     */
    public function checkUserAccess($userId, $controller, $action) : bool
    {
        $permissions = $this->userPermissionFunctionRepository->getPermissionsByUser($userId);
        foreach ($permissions as $permission) {
            /** @var PermissionFunctionWork $permission */
            if ($this->checkAllow($permission->short_code, $controller, $action->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Определяет, разрешает ли правило $rule получить доступ к экшну $controller/$action
     * @param $rule
     * @param $controller
     * @param $action
     * @return bool
     */
    public function checkAllow($rule, $controller, $action)
    {
        $permissions = $this->racConfig->getAllPermissions();
        return array_key_exists($rule, $permissions)
            && array_key_exists($controller, $permissions[$rule])
            && in_array($action, $permissions[$rule][$controller]);
    }

    public function isGuest() : bool
    {
        return Yii::$app->user->isGuest;
    }

    public function authId()
    {
        return Yii::$app->user->identity->getId();
    }
}