<?php

namespace backend\controllers\report\query;

use backend\forms\report\ManHoursReportForm;
use backend\helpers\DebugReportHelper;
use backend\services\report\ReportFacade;
use common\helpers\common\HeaderWizard;
use common\helpers\creators\ExcelCreator;
use Hidehalo\Nanoid\Client;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class ManHoursReportController extends Controller
{
    /**
     * @throws InvalidConfigException
     */
    public function actionManHours()
    {
        $form = Yii::createObject(ManHoursReportForm::class);
        if ($form->load(Yii::$app->request->post())) {
            $result = ReportFacade::generateManHoursReport($form);

            return $this->render('man-hours-result', [
                'manHoursResult' => $result['manHours'] ?? [],
                'participantsResult' => $result['participants'] ?? []
            ]);
        }

        return $this->render('man-hours', [
            'model' => $form
        ]);
    }

    public function actionDownloadDebugCsv(string $type)
    {
        if (Yii::$app->request->isPost) {
            switch ($type) {
                case ManHoursReportForm::MAN_HOURS_REPORT:
                    $csvHeader = DebugReportHelper::getManHoursReportHeaders();
                    break;
                default:
                    $csvHeader = DebugReportHelper::getParticipantsReportHeaders();
            }
            $data = json_decode(Yii::$app->request->post()['debugData'], true);
            $writer = new Csv(
                ExcelCreator::createCsvFile(
                    $data,
                    $csvHeader
                )
            );

            HeaderWizard::setCsvLoadHeaders((Yii::createObject(Client::class))->generateId(10) . '.csv');
            $writer->setDelimiter(';');
            $writer->setOutputEncoding('windows-1251');
            $writer->save('php://output');
            exit;
        }
        else {
            throw new BadRequestHttpException('Для данного эндпоинта допустимы только POST-запросы');
        }
    }
}