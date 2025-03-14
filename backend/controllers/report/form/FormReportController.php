<?php

namespace backend\controllers\report\form;

use backend\services\report\form\DodReportService;
use backend\services\report\form\StateAssignmentReportService;
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

    public function actionStateAssignment()
    {

    }
}