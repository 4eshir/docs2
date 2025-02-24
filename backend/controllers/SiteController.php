<?php

namespace backend\controllers;

use backend\forms\report\ManHoursReportForm;
use backend\helpers\DebugReportHelper;
use backend\services\report\ReportManHoursService;
use common\components\dictionaries\base\BranchDictionary;
use common\helpers\creators\ExcelCreator;
use common\models\LoginForm;
use frontend\helpers\HeaderWizard;
use Hidehalo\Nanoid\Client;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->session->get('previous_url')) {
                return $this->redirect(Yii::$app->session->get('previous_url'));
            }
            else {
                return $this->goBack();
            }
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionTest()
    {
        $service = Yii::createObject(ReportManHoursService::class);
        $res = $service->calculateParticipantsByPeriod(
            '2025-01-01',
            '2025-02-02',
            [BranchDictionary::TECHNOPARK, BranchDictionary::QUANTORIUM],
            [1, 2, 3, 4, 5],
            [1, 2],
            [0, 1],
            [ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN, ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER, ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN, ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER],
            ManHoursReportForm::PARTICIPANTS_ALL,
        );

        HeaderWizard::setCsvLoadHeaders((Yii::createObject(Client::class))->generateId(10) . '.csv');

        $writer = new Csv(
            ExcelCreator::createCsvFile(
                $res["debugData"],
                DebugReportHelper::getParticipantsReportHeaders()
            )
        );
        $writer->setDelimiter(';');
        $writer->setOutputEncoding('windows-1251');
        $writer->save('php://output');
        exit;
    }
}
