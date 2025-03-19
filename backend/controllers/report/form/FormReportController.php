<?php

namespace backend\controllers\report\form;

use backend\forms\report\DodForm;
use backend\forms\report\SAForm;
use backend\invokables\ReportDodLoader;
use backend\services\report\form\DodReportService;
use backend\services\report\form\StateAssignmentReportService;
use backend\services\report\ReportFacade;
use Yii;
use yii\web\Controller;

class FormReportController extends Controller
{
    private StateAssignmentReportService $stateAssignmentService;
    private DodReportService $dodReportService;

    public function __construct(
        $id,
        $module,
        StateAssignmentReportService $stateAssignmentService,
        DodReportService $dodReportService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->stateAssignmentService = $stateAssignmentService;
        $this->dodReportService = $dodReportService;
    }

    public function actionFormList()
    {
        return $this->render('form-list');
    }

    public function actionDod()
    {
        $model = new DodForm();

        if ($model->load(Yii::$app->request->post())) {
            $loader = new ReportDodLoader(
                'report_DOD.xlsx',
                'report_test.xlsx',
                ReportFacade::generateDod($model, $this->dodReportService)
            );
            $loader();
        }

        return $this->render('dod', [
            'model' => $model
        ]);
    }

    public function actionStateAssignment()
    {
        $model = new SAForm();

        if ($model->load(Yii::$app->request->post())) {
            echo '<pre>';
            var_dump(ReportFacade::generateSA($model, $this->stateAssignmentService));
            echo '</pre>';
            /*$loader = new ReportSALoader(
                'report_GZ.xlsx',
                'report_test.xlsx',
                ReportFacade::generateSA($model, $this->stateAssignmentService)
            );
            $loader();*/
        }

        return $this->render('state-assignment', [
            'model' => $model
        ]);
    }
}