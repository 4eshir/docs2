<?php

namespace frontend\controllers\educational;

use common\repositories\educational\CertificateRepository;
use common\repositories\educational\TrainingGroupParticipantRepository;
use frontend\components\GroupParticipantWidget;
use frontend\components\wizards\CertificateWizard;
use frontend\forms\certificate\CertificateForm;
use frontend\models\search\SearchCertificate;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\services\educational\CertificateService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class CertificateController extends Controller
{
    private CertificateRepository $repository;
    private CertificateService $service;

    public function __construct(
        $id,
        $module,
        CertificateRepository $repository,
        CertificateService $service,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repository = $repository;
        $this->service = $service;
    }

    public function actionIndex()
    {
        $searchModel = new SearchCertificate();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate($id = null)
    {
        $form = new CertificateForm(
            $this->service->buildGroupQuery($id),
            $this->service->buildParticipantQuery($id)
        );

        if ($form->load(Yii::$app->request->post())) {
            $certificateIds = $this->service->saveAllCertificates($form);
            $this->service->uploadCertificates($certificateIds);
            return $this->redirect(['download-archive']);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionDownloadArchive()
    {
        $this->service->downloadCertificates();
        return $this->redirect(['index']);
    }

    public function actionSendAll($groupId)
    {
        CertificateWizard::sendCertificates(
            $this->repository->getCertificatesByGroupId($groupId)
        );

        return $this->redirect(['/educational/training-group/view', 'id' => $groupId]);
    }

    public function actionGetGroups()
    {
        return [];
    }

    public function actionGetParticipants()
    {
        $groupIds = json_decode(Yii::$app->request->get('groupIds'));

        return $this->asJson([
            'gridHtml' => $this->renderPartial(GroupParticipantWidget::GROUP_PARTICIPANT_VIEW, [
                'dataProvider' => new ActiveDataProvider([
                    'query' => TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupIds])/*$this->participantRepository->getParticipantsFromGroups($groupIds)*/
                ]),
            ]),
        ]);
    }
}