<?php

namespace console\controllers;

use common\services\monitoring\PermissionLinksMonitor;
use Yii;
use yii\console\Controller;

class TempController extends Controller
{
    public function actionCheck()
    {
        $monitor = Yii::createObject(PermissionLinksMonitor::class);
        var_dump($monitor->checkUnlinkedActions()[0]);
        var_dump($monitor->checkUnlinkedActions()[1]);
    }
}