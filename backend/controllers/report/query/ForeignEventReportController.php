<?php

namespace backend\controllers\report\query;

use backend\forms\report\ForeignEventReportForm;
use backend\forms\report\ManHoursReportForm;
use backend\services\report\ReportFacade;
use backend\services\report\ReportForeignEventService;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;

class ForeignEventReportController extends Controller
{
    /**
     * @throws InvalidConfigException
     */
    public function actionForeignEvent()
    {
        $form = Yii::createObject(ForeignEventReportForm::class);
        if ($form->load(Yii::$app->request->post())) {
            $result = ReportFacade::generateParticipantsReport($form);

            return $this->render('foreign-event-result', [
                'resultData' => $result['result'] ?? [],
                'debugData' => $result['debug'] ?? []
            ]);
        }

        return $this->render('foreign-event', [
            'model' => $form
        ]);
    }

    public function actionForeignEventResult()
    {

    }
}