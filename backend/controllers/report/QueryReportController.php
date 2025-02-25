<?php

namespace backend\controllers\report;

use backend\forms\report\ManHoursReportForm;
use backend\services\report\ReportFacade;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;

class QueryReportController extends Controller
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
}