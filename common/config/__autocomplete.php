<?php

use common\components\access\RacComponent;
use common\components\access\RulesConfig;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\CategorySmspDictionary;
use common\components\dictionaries\base\CompanyTypeDictionary;
use common\components\dictionaries\base\DocumentTypeDictionary;
use common\components\dictionaries\base\EventFormDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\components\dictionaries\base\EventTypeDictionary;
use common\components\dictionaries\base\EventWayDictionary;
use common\components\dictionaries\base\OwnershipTypeDictionary;
use common\components\dictionaries\base\ParticipationScopeDictionary;
use common\components\dictionaries\base\PersonalDataDictionary;
use common\components\dictionaries\base\RegulationTypeDictionary;
use common\components\dictionaries\base\SendMethodDictionary;
use common\components\dictionaries\TableDictionary;

/**
 * This class only exists here for IDE (PHPStorm/Netbeans/...) autocompletion.
 * This file is never included anywhere.
 * Adjust this file to match classes configured in your application config, to enable IDE autocompletion for custom components.
 * Example: A property phpdoc can be added in `__Application` class as `@property \vendor\package\Rollbar|__Rollbar $rollbar` and adding a class in this file
 * ```php
 * // @property of \vendor\package\Rollbar goes here
 * class __Rollbar {
 * }
 * ```
 */
class Yii {
    /**
     * @var \yii\web\Application|\yii\console\Application|__Application
     */
    public static $app;
}
/**
 * @property yii\rbac\DbManager $authManager
 * @property \yii\web\User|__WebUser $user
 * @property RulesConfig $rulesConfig
 * @property BranchDictionary $branches
 * @property SendMethodDictionary $sendMethods
 * @property CompanyTypeDictionary $companyType
 * @property CategorySmspDictionary $categorySmsp
 * @property OwnershipTypeDictionary $ownershipType
 * @property RegulationTypeDictionary $regulationType
 * @property DocumentTypeDictionary $documentType
 * @property TableDictionary $tables
 * @property PersonalDataDictionary $personalData
 * @property EventFormDictionary $eventForm
 * @property EventLevelDictionary $eventLevel
 * @property EventTypeDictionary $eventType
 * @property EventWayDictionary $eventWay
 * @property ParticipationScopeDictionary $participationScope
 * @property RacComponent $rac
 */
class __Application {
}

/**
 * @property app\models\User $identity
 */
class __WebUser {
}
