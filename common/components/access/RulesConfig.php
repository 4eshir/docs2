<?php

namespace common\components\access;

use Yii;

class RulesConfig
{
    // основные связи правил с экшнами контроллеров
    private $permissionActionLinks = [
        'add_group' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'create',
                'generate-journal',
                'delete-journal'
            ]
        ],

        'view_self_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
                'download-plan'
            ]
        ],

        'view_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
                'download-plan'
            ]
        ],

        'view_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
                'download-plan'
            ]
        ],

        'edit_self_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'base-form',
                'participant-form',
                'schedule-form',
                'pitch-form',
                'group-deletion',
                'delete-file',
                'create-lesson-themes',
                'delete-lesson',
                'update-lesson',
                'sub-auds'
            ]
        ],

        'edit_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'base-form',
                'participant-form',
                'schedule-form',
                'pitch-form',
                'group-deletion',
                'delete-file',
                'create-lesson-themes',
                'delete-lesson',
                'update-lesson',
                'sub-auds',
                'create-protocol'
            ]
        ],

        'edit_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'base-form',
                'participant-form',
                'schedule-form',
                'pitch-form',
                'group-deletion',
                'delete-file',
                'create-lesson-themes',
                'delete-lesson',
                'update-lesson',
                'sub-auds',
                'create-protocol'
            ]
        ],

        'delete_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'delete'
            ]
        ],

        'delete_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'delete'
            ]
        ],

        'archive_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'archive',
                'archive-save',
            ]
        ],

        'archive_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'archive',
                'archive-save',
            ]
        ],

        'forgive_study_errors' => [

        ],

        'forgive_base_errors' => [

        ],

        'delete_participants' => [
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'delete'
            ]
        ],

        'merge_participants' => [
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'merge-participant',
                'info'
            ]
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
                'relevance',
                'relevance-save',
            ]
        ],

        'view_event_orders' => [
            \frontend\controllers\order\OrderEventController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_event_orders' => [
            \frontend\controllers\order\OrderEventController::class => [
                'create',
                'update',
                'delete-file',
                'act',
                'delete-people',
                'act-delete'
            ]
        ],

        'view_study_orders' => [
            \frontend\controllers\order\OrderTrainingController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_study_orders' => [
            \frontend\controllers\order\OrderTrainingController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'get-list-by-branch',
                'set-name-order',
                'get-group-by-branch',
                'get-group-participants-by-branch',
                'set-preamble'
            ]
        ],

        'view_base_orders' => [
            \frontend\controllers\order\OrderMainController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_base_orders' => [
            \frontend\controllers\order\OrderMainController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'delete-document'
            ]
        ],

        'gen_report_query' => [

        ],

        'gen_report_forms' => [

        ],

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

        'view_events' => [
            \frontend\controllers\event\OurEventController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_events' => [
            \frontend\controllers\event\OurEventController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ]
        ],

        'view_foreign_events' => [
            \frontend\controllers\event\ForeignEventController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_foreign_events' => [
            \frontend\controllers\event\ForeignEventController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'update-participant',
                'update-achievement',
                'delete-achievement'
            ]
        ],

        'view_local_resp' => [
            \frontend\controllers\responsibility\LocalResponsibilityController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
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
        ],

        'view_users' => [
            \frontend\controllers\user\UserController::class => [
                'index'
            ]
        ],

        'edit_users' => [

        ],

        'edit_permissions' => [

        ],

        'create_certificates' => [

        ],

        'delete_certificates' => [

        ],

        'allow_base_admin' => [
            \backend\controllers\UserController::class => [
                'index',
                'view',
                'create',
                'update',
                'tokens',
                'delete-token'
            ]
        ],

        'allow_extended_admin' => [

        ],

        'view_certificate_template' => [

        ],

        'edit_certificate_template' => [

        ],

        'view_material_obj' => [

        ],

        'edit_material_obj' => [

        ],

        'move_material_obj' => [

        ],

        'view_dictionaries' => [
            \frontend\controllers\dictionaries\AuditoriumController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
            ],
            \frontend\controllers\dictionaries\CompanyController::class => [
                'index',
                'view',
            ],
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'index',
                'view',
            ],
            \frontend\controllers\dictionaries\PeopleController::class => [
                'index',
                'view',
            ],
            \frontend\controllers\dictionaries\PositionController::class => [
                'index',
                'view',
            ],
        ],

        'edit_dictionaries' => [
            \frontend\controllers\dictionaries\AuditoriumController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ],
            \frontend\controllers\dictionaries\CompanyController::class => [
                'create',
                'update',
                'delete'
            ],
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'create',
                'update',
                'delete',
                'file-load',
                'check-correct',
            ],
            \frontend\controllers\dictionaries\PeopleController::class => [
                'create',
                'update',
                'delete',
                'delete-position'
            ],
            \frontend\controllers\dictionaries\PositionController::class => [
                'create',
                'update',
                'delete'
            ],
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