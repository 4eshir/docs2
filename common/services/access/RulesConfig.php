<?php

namespace common\services\access;

use DomainException;

class RulesConfig
{
    // основные связи правил с экшнами контроллеров
    private $permissionActionLinks = [
        'add_group' => [
            \frontend\controllers\SiteController::class => [
                'index',
            ],
            \backend\controllers\SiteController::class => [
                'create',
            ],
        ]
    ];

    // системные экшны, которые не должны учитываться при мониторинге
    private $systemActions = [
        \frontend\controllers\SiteController::class => [
            'login',
        ],
    ];

    /**
     * Определяет, разрешает ли правило $rule получить доступ к экшну $controller/$action
     * @param $rule
     * @param $controller
     * @param $action
     * @return bool
     */
    public function checkAllow($rule, $controller, $action)
    {
        return array_key_exists($rule, $this->permissionActionLinks)
            && array_key_exists($controller, $this->permissionActionLinks[$rule])
            && in_array($action, $this->permissionActionLinks[$rule][$controller]);
    }

    public function getPermissionsName()
    {
        return array_keys($this->permissionActionLinks);
    }

    public function getAllPermissions()
    {
        return $this->permissionActionLinks;
    }

    public function getAllControllers() {
        $keys = [];

        foreach ($this->permissionActionLinks as $group => $controllers) {
            $controllerKeys = array_keys($controllers);
            $keys = array_merge($keys, $controllerKeys);
        }

        return $keys;
    }

    public function getAllActionsByController($controllerName)
    {
        $actions = [];
        foreach ($this->permissionActionLinks as $permission) {
            if (array_key_exists($controllerName, $permission)) {
                $actions = array_merge($actions, $permission[$controllerName]);
            }
        }

        if (array_key_exists($controllerName, $this->systemActions)) {
            $actions = array_diff($this->systemActions[$controllerName], $actions);
        }

        return $actions;
    }
}