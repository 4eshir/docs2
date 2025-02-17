<?php

namespace console\controllers;

use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use common\components\logger\search\MethodSearchData;
use common\components\logger\search\SearchLog;
use common\components\logger\SearchLogFacade;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class TempController extends Controller
{
    public function actionCheck()
    {
        $provider = SearchLog::byParams(
            [LogInterface::LVL_INFO],
            '1900-01-01',
            '1900-01-01',
            [2],
            [],
            ''
        );
        //$provider->setMethodSearchData(MethodSearchData::create(['peoples-controller']));
        var_dump(
            ArrayHelper::getColumn(
                SearchLogFacade::findLogs($provider),
                'id'
            )
        );
    }
}