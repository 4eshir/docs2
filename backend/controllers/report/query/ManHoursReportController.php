<?php

namespace backend\controllers\report;

use backend\forms\report\ManHoursReportForm;
use Yii;
use yii\web\Controller;

class ManHoursReportController extends Controller
{
    public function actionManHours()
    {
        $form = Yii::createObject(ManHoursReportForm::class);

        return $this->render('man-hours', [
            'model' => $form
        ]);
    }
}