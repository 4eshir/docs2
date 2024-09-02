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
        'ownershipType' => [
            'class' => 'common\\components\\dictionaries\\base\\OwnershipTypeDictionary',
        ],
        'regulationType' => [
            'class' => 'common\\components\\dictionaries\\base\\RegulationTypeDictionary',
        ],
        'rac' => [
            'class' => 'common\\components\\access\\RacComponent',
        ],
    ],
];