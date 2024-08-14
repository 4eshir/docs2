<?php

namespace common\components\access;

use Yii;

class RulesConfig
{
    // основные связи правил с экшнами контроллеров
    private $permissionActionLinks = [
        'view_doc_in' => [
            \frontend\controllers\document\DocumentInController::class => [
                'index',
                'view',
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