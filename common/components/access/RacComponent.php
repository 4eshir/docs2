<?php

namespace common\components\access;

use common\repositories\general\UserRepository;
use common\repositories\rac\UserPermissionFunctionRepository;
use frontend\models\work\rac\PermissionFunctionWork;
use Yii;
use yii\helpers\ArrayHelper;

class RacComponent
{
    private UserPermissionFunctionRepository $userPermissionFunctionRepository;
    private UserRepository $userRepository;
    private RulesConfig $racConfig;
    private AuthDataCache $authCache;
    private $permissions = [];

    public function __construct(
        UserPermissionFunctionRepository $userPermissionFunctionRepository,
        RulesConfig $racConfig,
        UserRepository $userRepository,
        AuthDataCache $authCache
    )
    {
        $this->userPermissionFunctionRepository = $userPermissionFunctionRepository;
        $this->racConfig = $racConfig;
        $this->userRepository = $userRepository;
        $this->authCache = $authCache;
    }

    public function init()
    {
        if (Yii::$app->user->identity->getId()) {
            $userId = Yii::$app->user->identity->getId();
            $permissions = $this->userPermissionFunctionRepository->getPermissionsByUser($userId);
            $this->permissions = $permissions;
            $this->authCache->loadDataFromPermissions($permissions, $userId);
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
     *
     * @param $userId
     * @param $controller
     * @param $action
     * @return bool
     */
    public function checkUserAccess($userId, $controller, $action) : bool
    {
        $this->authCache->loadDataFromDB($userId);
        $permissions = $this->authCache->getAllPermissionsFromUser($userId);
        if (!$permissions) {
            $permissions = ArrayHelper::getColumn($this->userPermissionFunctionRepository->getPermissionsByUser($userId), 'short_code');
        }

        foreach ($permissions as $permission) {
            if ($this->checkAllow($permission, $controller, $action->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Определяет, разрешает ли правило $rule получить доступ к экшну $controller/$action
     *
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