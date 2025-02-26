<?php

namespace frontend\controllers\educational;

use common\repositories\educational\VisitRepository;
use frontend\forms\journal\JournalForm;
use frontend\forms\journal\ThematicPlanForm;
use frontend\models\work\educational\journal\ParticipantLessons;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\services\educational\JournalService;
use Yii;
use yii\web\Controller;

class JournalController extends Controller
{
    private JournalService $service;

    public function __construct(
        $id,
        $module,
        JournalService $service,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionView($id)
    {
        $form = new JournalForm($id);
        $plan = new ThematicPlanForm($id);

        return $this->render('view', [
            'model' => $form,
            'plan' => $plan
        ]);
    }

    public function actionUpdate($id)
    {
        $form = new JournalForm($id);

        if ($form->load(Yii::$app->request->post())) {
            foreach ($form->participantLessons as $participantLesson) {
                /** @var ParticipantLessons $participantLesson */
                $this->service->setVisitStatusParticipant(
                    $participantLesson->trainingGroupParticipantId,
                    $participantLesson->lessonIds
                );
                $this->service->setParticipantFinishData(
                    $participantLesson->trainingGroupParticipantId,
                    $participantLesson->groupProjectThemeId,
                    $participantLesson->points,
                    $participantLesson->successFinishing
                );
            }

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $form
        ]);
    }

    public function actionEditPlan($id)
    {
        $form = new ThematicPlanForm($id);

        if ($form->load(Yii::$app->request->post())) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('edit-plan', [
            'model' => $form
        ]);
    }
}