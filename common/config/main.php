<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'params' => [
        'mainCompanyId' => 1, //ID компании-владельца системы
        'yandexApiKey' => 'y0_AgAEA7qkEK7HAAn5LwAAAADkMhh1CPjqd4DtS52DG7Vyd3i0JNf-NxY',
    ],
    'components' => [

        'redis' => [
            'class' => 'common\\components\\RedisComponent',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],

        'cache' => [
            'class' => 'yii\\caching\\FileCache',
        ],

        'rulesConfig' => [
            'class' => 'common\\components\\access\\RulesConfig',
        ],
        'branches' => [
            'class' => 'common\\components\\dictionaries\\base\\BranchDictionary',
        ],
        'sendMethods' => [
            'class' => 'common\\components\\dictionaries\\base\\SendMethodDictionary',
        ],
        'companyType' => [
            'class' => 'common\\components\\dictionaries\\base\\CompanyTypeDictionary',
        ],
        'categorySmsp' => [
            'class' => 'common\\components\\dictionaries\\base\\CategorySmspDictionary',
        ],
        'documentStatus' => [
            'class' => 'common\\components\\dictionaries\\base\\DocumentStatusDictionary',
        ],
        'ownershipType' => [
            'class' => 'common\\components\\dictionaries\\base\\OwnershipTypeDictionary',
        ],
        'regulationType' => [
            'class' => 'common\\components\\dictionaries\\base\\RegulationTypeDictionary',
        ],
        'tables' => [
            'class' => 'common\\components\\dictionaries\\TableDictionary',
        ],
        'eventForm' => [
            'class' => 'common\\components\\dictionaries\\base\\EventFormDictionary',
        ],
        'eventLevel' => [
            'class' => 'common\\components\\dictionaries\\base\\EventLevelDictionary',
        ],
        'eventType' => [
            'class' => 'common\\components\\dictionaries\\base\\EventTypeDictionary',
        ],
        'eventWay' => [
            'class' => 'common\\components\\dictionaries\\base\\EventWayDictionary',
        ],
        'participationScope' => [
            'class' => 'common\\components\\dictionaries\\base\\ParticipationScopeDictionary',
        ],
        'personalData' => [
            'class' => 'common\\components\\dictionaries\\base\\PersonalDataDictionary',
        ],
        'thematicDirection' => [
            'class' => 'common\\components\\dictionaries\\base\\ThematicDirectionDictionary',
        ],
        'focus' => [
            'class' => 'common\\components\\dictionaries\\base\\FocusDictionary',
        ],
        'allowRemote' => [
            'class' => 'common\\components\\dictionaries\\base\\AllowRemoteDictionary',
        ],
        'certificateType' => [
            'class' => 'common\\components\\dictionaries\\base\\CertificateTypeDictionary',
        ],
        'controlType' => [
            'class' => 'common\\components\\dictionaries\\base\\ControlTypeDictionary',
        ],
        'auditoriumType' => [
            'class' => 'common\\components\\dictionaries\\base\\AuditoriumTypeDictionary',
        ],
        'responsibilityType' => [
            'class' => 'common\\components\\dictionaries\\base\\ResponsibilityTypeDictionary',
        ],
        'rac' => [
            'class' => 'common\\components\\access\\RacComponent',
        ],
        'nomenclature' => [
            'class' => 'common\\components\\dictionaries\\base\\NomenclatureDictionary',
        ],
        'projectType' => [
            'class' => 'common\\components\\dictionaries\\base\\ProjectTypeDictionary',
        ],
        'frontUrls' => [
            'class' => 'frontend\\components\\routes\\Urls',
        ]
    ],
];