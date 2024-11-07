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
                'get-file',
                'dependency-dropdown',
            ],
        ],

        'edit_doc_in' => [
            \frontend\controllers\document\DocumentInController::class => [
                'create',
                'update',
                'delete',
                'reserve',
                'delete-file',
            ],
        ],

        'view_doc_out' => [
            \frontend\controllers\document\DocumentOutController::class => [
                'index',
                'view',
                'get-file',
                'dependency-dropdown',
            ],
        ],

        'edit_doc_out' => [
            \frontend\controllers\document\DocumentOutController::class => [
                'create',
                'update',
                'delete',
                'reserve',
                'delete-file',
            ],
        ],

        'view_base_regulations' => [
            \frontend\controllers\regulation\RegulationController::class => [
                'index',
                'view',
                'get-file',
            ],
        ],

        'edit_base_regulations' => [
            \frontend\controllers\regulation\RegulationController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ],
        ],

        'view_event_regulations' => [
            \frontend\controllers\regulation\RegulationEventController::class => [
                'index',
                'view',
                'get-file',
            ],
        ],

        'edit_event_regulations' => [
            \frontend\controllers\regulation\RegulationEventController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ],
        ],

        'view_training_programs' => [
            \frontend\controllers\educational\TrainingProgramController::class => [
                'index',
                'view',
                'get-file',
            ]
        ],

        'edit_training_programs' => [
            \frontend\controllers\educational\TrainingProgramController::class => [
                'create',
                'update',
                'delete',
                'update-theme',
                'delete-theme',
                'delete-author',
                'delete-file',
            ]
        ],

        'view_local_resp' => [
            \frontend\controllers\responsibility\LocalResponsibilityController::class => [
                'index',
                'view',
                'get-file',
            ]
        ],

        'edit_local_resp' => [
            \frontend\controllers\responsibility\LocalResponsibilityController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'get-auditorium',
            ]
        ]
    ];

    // системные экшны, которые не должны учитываться при мониторинге
    private $systemActions = [
        \frontend\controllers\SiteController::class => [
            'index',
            'login',
            'logout',
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