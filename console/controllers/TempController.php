<?php

namespace console\controllers;

use backend\forms\report\ManHoursReportForm;
use backend\services\report\ReportManHoursService;
use common\components\dictionaries\base\BranchDictionary;
use common\components\logger\base\LogInterface;
use common\components\logger\LogFactory;
use common\components\logger\search\CrudSearchData;
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
        $provider = SearchLog::byUserIds(
            [1, 2]
        );

        $provider->setMethodSearchData(
            MethodSearchData::create(
                ['document-in-controller']
            )
        );

        $provider->setCrudSearchData(
            CrudSearchData::create(
                'DELETE'
            )
        );

        var_dump(ArrayHelper::getColumn(SearchLogFacade::findLogs($provider), 'id'));
    }

    public function actionReport()
    {
        $service = Yii::createObject(ReportManHoursService::class);
        var_dump($service->calculateManHours(
            '2025-01-01',
            '2025-02-02',
            [BranchDictionary::TECHNOPARK],
            [1, 2, 3, 4, 5],
            [1, 2],
            [0, 1],
            ManHoursReportForm::MAN_HOURS_FAIR
        ));
    }
}