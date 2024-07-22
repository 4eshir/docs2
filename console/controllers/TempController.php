<?php

namespace console\controllers;

use common\models\scaffold\People;
use common\models\work\general\PeopleWork;
use common\services\monitoring\PermissionLinksMonitor;
use Yii;
use yii\console\Controller;

class TempController extends Controller
{
    public function actionCheck()
    {
        var_dump((Yii::createObject(PeopleWork::class))->hasAttribute('patronymic'));
    }
}