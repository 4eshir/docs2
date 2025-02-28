<?php

namespace frontend\controllers\educational;

use common\repositories\educational\TrainingGroupParticipantRepository;
use frontend\components\GroupParticipantWidget;
use frontend\forms\certificate\CertificateForm;
use frontend\models\search\SearchCertificate;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class CertificateController extends Controller
{
    private TrainingGroupParticipantRepository $participantRepository;

    public function __construct(
        $id,
        $module,
        TrainingGroupParticipantRepository $participantRepository,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->participantRepository = $participantRepository;
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

    public function actionCreate()
    {
        $form = new CertificateForm();

        if (!is_array(Yii::$app->request->post())) {
            //var_dump(Yii::$app->request->post()['group-participant-selection']);die;
            return $this->redirect(['view', 'id' => $form->id]);
        }

        return $this->render('create', [
            'model' => $form,
        ]);
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