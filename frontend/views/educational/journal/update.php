<?php

use frontend\forms\journal\JournalForm;
use frontend\models\work\educational\journal\VisitWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model JournalForm */

?>

<div class="journal-edit">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="journal-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'groupId')->hiddenInput()->label(false) ?>

        <table class="table table-bordered">
            <?php foreach ($model->participantLessons as $participantLesson): ?>
                <tr>
                    <td>
                        Participant ID: <?= $participantLesson->trainingGroupParticipantId ?>
                    </td>
                    <?php foreach ($participantLesson->lessonIds as $index => $lesson): ?>
                        <td>
                            <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]lessonId")->hiddenInput(['value' => $lesson->lessonId])->label(false) ?>
                            <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]status")
                                ->dropDownList([
                                    VisitWork::NONE => '---',
                                    VisitWork::ATTENDANCE => 'Я',
                                    VisitWork::NO_ATTENDANCE => 'Н',
                                    VisitWork::DISTANCE => 'Д'
                                ])
                                ->label("Lesson ID: {$lesson->lessonId}"); ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
