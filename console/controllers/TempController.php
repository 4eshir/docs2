<?php

namespace console\controllers;

use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use Yii;
use yii\console\Controller;

class TempController extends Controller
{
    public function actionCheck()
    {
        LogFactory::createBaseLog(
            '2025-02-13',
            LogInterface::LVL_INFO,
            1,
            'TEST TEST'
        );

        LogFactory::createMethodLog(
            '2025-02-13',
            LogInterface::LVL_INFO,
            1,
            'METHOD METHOD',
            'document-in-controller',
            'index',
            LogInterface::TYPE_METHOD
        );
    }
}