<?php

namespace backend\controllers\report\query;

use backend\forms\report\ForeignEventReportForm;
use backend\forms\report\ManHoursReportForm;
use backend\helpers\DebugReportHelper;
use backend\services\report\QueryReportService;
use backend\services\report\ReportFacade;
use backend\services\report\ReportForeignEventService;
use common\helpers\common\HeaderWizard;
use common\helpers\creators\ExcelCreator;
use Hidehalo\Nanoid\Client;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class ForeignEventReportController extends Controller
{
    private QueryReportService $service;

    public function __construct(
        $id,
        $module,
        QueryReportService $service,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionForeignEvent()
    {
        $form = Yii::createObject(ForeignEventReportForm::class);
        if ($form->load(Yii::$app->request->post())) {
            $result = ReportFacade::generateParticipantsReport($form);

            return $this->render('foreign-event-result', [
                'eventResult' => $result ?? []
            ]);
        }

        return $this->render('foreign-event', [
            'model' => $form
        ]);
    }

    public function actionDownloadDebugCsv()
    {
        if (Yii::$app->request->isPost) {
            $csvHeader = DebugReportHelper::getEventReportHeaders();
            $this->service->downloadCsvDebugFile(Yii::$app->request->post()['debugData'], $csvHeader);
        }
        else {
            throw new BadRequestHttpException('Для данного эндпоинта допустимы только POST-запросы');
        }
    }
}