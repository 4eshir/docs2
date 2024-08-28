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

        // another data

        'rulesConfig' => [
            'class' => 'common\\services\\access\\RulesConfig',
        ],
        'branches' => [
            'class' => 'common\\components\\dictionaries\\BranchDictionary',
        ],
        'sendMethods' => [
            'class' => 'common\\components\\dictionaries\\SendMethodDictionary',
        ],
        'companyType' => [
            'class' => 'common\\components\\dictionaries\\CompanyTypeDictionary',
        ],
        'categorySmsp' => [
            'class' => 'common\\components\\dictionaries\\CategorySmspDictionary',
        ],
        'ownershipType' => [
            'class' => 'common\components\dictionaries\OwnershipTypeDictionary',
        ],
        'rac' => [
            'class' => 'common\\components\\access\\RacComponent',
        ],
    ],
];