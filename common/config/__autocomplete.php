<?php

use common\components\access\RacComponent;
use common\components\access\RulesConfig;
use common\components\dictionaries\base\AllowRemoteDictionary;
use common\components\dictionaries\base\AuditoriumTypeDictionary;
use common\components\dictionaries\base\BranchDictionary;
use common\components\dictionaries\base\CategorySmspDictionary;
use common\components\dictionaries\base\CertificateTypeDictionary;
use common\components\dictionaries\base\CompanyTypeDictionary;
use common\components\dictionaries\base\ControlTypeDictionary;
use common\components\dictionaries\base\DocumentStatusDictionary;
use common\components\dictionaries\base\DocumentTypeDictionary;
use common\components\dictionaries\base\EventFormDictionary;
use common\components\dictionaries\base\EventLevelDictionary;
use common\components\dictionaries\base\EventTypeDictionary;
use common\components\dictionaries\base\EventWayDictionary;
use common\components\dictionaries\base\FocusDictionary;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\components\dictionaries\base\OwnershipTypeDictionary;
use common\components\dictionaries\base\ParticipationScopeDictionary;
use common\components\dictionaries\base\PersonalDataDictionary;
use common\components\dictionaries\base\ProjectTypeDictionary;
use common\components\dictionaries\base\RegulationTypeDictionary;
use common\components\dictionaries\base\ResponsibilityTypeDictionary;
use common\components\dictionaries\base\SendMethodDictionary;
use common\components\dictionaries\base\ThematicDirectionDictionary;
use common\components\dictionaries\TableDictionary;
use common\components\RedisComponent;
use frontend\components\routes\Urls;
use yii\caching\FileCache;

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
 * @property RedisComponent $redis
 * @property FileCache $cache
 * @property RulesConfig $rulesConfig
 * @property BranchDictionary $branches
 * @property NomenclatureDictionary $nomenclature
 * @property SendMethodDictionary $sendMethods
 * @property CompanyTypeDictionary $companyType
 * @property CategorySmspDictionary $categorySmsp
 * @property OwnershipTypeDictionary $ownershipType
 * @property RegulationTypeDictionary $regulationType
 * @property DocumentTypeDictionary $documentType
 * @property DocumentStatusDictionary $documentStatus
 * @property TableDictionary $tables
 * @property PersonalDataDictionary $personalData
 * @property EventFormDictionary $eventForm
 * @property EventLevelDictionary $eventLevel
 * @property EventTypeDictionary $eventType
 * @property EventWayDictionary $eventWay
 * @property ParticipationScopeDictionary $participationScope
 * @property ThematicDirectionDictionary $thematicDirection
 * @property FocusDictionary $focus
 * @property AllowRemoteDictionary $allowRemote
 * @property CertificateTypeDictionary $certificateType
 * @property ControlTypeDictionary $controlType
 * @property AuditoriumTypeDictionary $auditoriumType
 * @property ResponsibilityTypeDictionary $responsibilityType
 * @property ProjectTypeDictionary $projectType
 * @property RacComponent $rac
 * @property Urls $frontUrls
 */
class __Application {
}

/**
 * @property app\models\User $identity
 */
class __WebUser {
}
