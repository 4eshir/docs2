<?php

namespace frontend\controllers\educational;

use common\helpers\ButtonsFormatter;
use common\helpers\html\HtmlBuilder;
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

        $links = array_merge(
            ButtonsFormatter::anyOneLink(
                'Редактировать журнал',
                Yii::$app->frontUrls::JOURNAL_UPDATE,
                ButtonsFormatter::BTN_PRIMARY,
                '',
                ButtonsFormatter::createParameterLink($id)
            ),
            ButtonsFormatter::anyOneLink(
                'Редактировать ТП',
                Yii::$app->frontUrls::JOURNAL_EDIT_PLAN,
                ButtonsFormatter::BTN_SUCCESS,
                '',
                ButtonsFormatter::createParameterLink($id)
            )
        );
        $buttonHtml = HtmlBuilder::createGroupButton($links);

        $otherLinks = array_merge(
            ButtonsFormatter::anyOneLink(
                'Создать ТП',
                Yii::$app->frontUrls::LESSON_THEMES_CREATE,
                ButtonsFormatter::BTN_SECONDARY,
                '',
                ButtonsFormatter::createParameterLink($id, 'groupId')
            ),
            ButtonsFormatter::anyOneLink(
                'Очистить ТП',
                Yii::$app->frontUrls::JOURNAL_DELETE_PLAN,
                ButtonsFormatter::BTN_WARNING,
                '',
                ButtonsFormatter::createParameterLink($id)
            ),
            ButtonsFormatter::anyOneLink(
                'Удалить журнал',
                Yii::$app->frontUrls::JOURNAL_DELETE,
                ButtonsFormatter::BTN_DANGER,
                '',
                ButtonsFormatter::createParameterLink($id),
            ),
        );
        $otherButtonHtml = HtmlBuilder::createGroupButton($otherLinks);

        return $this->render('view', [
            'model' => $form,
            'plan' => $plan,
            'buttonsAct' => $buttonHtml,
            'otherButtonsAct' => $otherButtonHtml,
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
            $this->service->saveThematicPlan($form->lessonThemes);
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('edit-plan', [
            'model' => $form
        ]);
    }

    public function actionDeletePlan($id)
    {
        $this->service->deleteThematicPlan($id);
        return $this->redirect(['view', 'id' => $id]);
    }
}