<?php

use common\helpers\html\HtmlBuilder;
use frontend\forms\journal\JournalForm;
use frontend\models\work\educational\journal\VisitWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model JournalForm */

$this->title = 'Редактирование журнала ' . $model->getTrainingGroupNumber();
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_INDEX]];
$this->params['breadcrumbs'][] = ['label' => 'Группа ' . $model->getTrainingGroupNumber(), 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = ['label' => 'Электронный журнал', 'url' => [Yii::$app->frontUrls::JOURNAL_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="journal-edit">

    <?php $form = ActiveForm::begin(); ?>

    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="flexx space">
            <div class="flexx">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="flexx">
                <?= Html::button('Все явки', ['class' => 'btn btn-primary fill-all-status']) ?>
                <?= Html::button('Индивидуальные статусы', ['class' => 'btn btn-success individual-status']) ?>
            </div>
        </div>
    </div>

    <div class="journal-form">

        <?= $form->field($model, 'groupId')->hiddenInput()->label(false) ?>
        <div class="card no-flex">
            <div class="table-topic">
                Электронный журнал
            </div>
            <div class="table-block scroll">
                <table>
                    <thead>
                    <tr>
                        <th>ФИО</th>
                        <th colspan="<?= $model->getLessonsCount() ?>">Расписание</th>
                        <th colspan="<?= $model->getColspanControl() ?>">Итоговый контроль</th>
                    </tr>
                    <tr>
                        <td>учащегося</td>
                        <?php foreach ($model->getDateLessons() as $dateLesson): ?>
                            <td class="lessons-date"> <?= $dateLesson ?>  </td>
                        <?php endforeach; ?>
                        <td style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>">Тема проекта</td>
                        <td style="display: <?= $model->isControlWorkCertificate() ? 'block' : 'none';?>">Оценка</td>
                        <td>Успешное завершение</td>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($model->participantLessons as $participantLesson): ?>
                        <tr>
                            <td>
                                <?= $model->getPrettyParticipant($participantLesson->participant); ?>
                            </td>
                            <?php foreach ($participantLesson->lessonIds as $index => $lesson): ?>
                                <td class="status-participant">
                                    <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]lessonId")
                                        ->hiddenInput(['value' => $lesson->lessonId])
                                        ->label(false) ?>

                                    <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]status")
                                        ->dropDownList([
                                            VisitWork::NONE => '---',
                                            VisitWork::ATTENDANCE => 'Я',
                                            VisitWork::NO_ATTENDANCE => 'Н',
                                            VisitWork::DISTANCE => 'Д'
                                        ])
                                        ->label(false); ?>
                                </td>
                            <?php endforeach; ?>
                            <td style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]groupProjectThemeId")->dropDownList(
                                    ArrayHelper::map($model->availableThemes, 'id', 'projectThemeWork.name'),
                                    ['prompt' => '']
                                )->label(false) ?>
                            </td>
                            <td class="status-participant" style="display: <?= $model->isControlWorkCertificate() ? 'block' : 'none';?>">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]points")->textInput(['type' => 'number'])->label(false) ?>
                            </td>
                            <td class="status-participant">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]successFinishing")->checkbox()->label(false) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php ActiveForm::end(); ?>
    <?= HtmlBuilder::upButton();?>
</div>