<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\work\TeacherParticipantBranchWork;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\work\TeacherParticipantWork */

$tPart = \app\models\work\TeacherParticipantWork::find()->where(['id' => $model->teacher_participant_id])->one();

$this->title = 'Редактировать: ' . $tPart->actString;
$this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => mb_substr($tPart->foreignEventWork->name, 0, 25, 'UTF-8').'...', 'url' => ['foreign-event/update', 'id' => $tPart->foreignEventWork->id]];
$this->params['breadcrumbs'][] = ['label' => $tPart->participantWork->fullName, 'url' => ['foreign-event-participants/view', 'id' => $tPart->participant_id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<style type="text/css">
    .toggle-wrapper {
        margin-top: 2.5em;
        position: relative;
        display: flex;
        align-items: center;
        column-gap: .25em;
        margin-bottom: 2.5em;
    }

    .toggle-checkbox:not(:checked) + .off,
    .toggle-checkbox:checked ~ .on {
        font-weight: 700;
    }

    .toggle-checkbox {
        -webkit-appearance: none;
        appearance: none;
        position: absolute;
        z-index: 1;
        border-radius: 3.125em;
        width: 4.05em;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        margin-left: 4em!important;
    }

    .toggle-container {
        position: relative;
        border-radius: 3.125em;
        width: 4.05em;
        height: 1.5em;
        background-color: #ccc;
        background-size: .125em .125em;
    }

    .toggle-button {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: .0625em;
        left: .0625em;
        border-radius: inherit;
        width: 2.55em;
        height: calc(100% - .125em);
        background-color: #FFA23A;
        box-shadow: 0 .125em .25em rgb(0 0 0 / .6);
        transition: left .4s;

    .toggle-checkbox:checked ~ .toggle-container > & {
        left: 1.4375em;
    }

    &::before {
         content: '';
         position: absolute;
         top: inherit;
         border-radius: inherit;
         width: calc(100% - .375em);
         height: inherit;
     }

    &::after {
         content: '';
         position: absolute;
         width: .5em;
         height: 38%;
     }
    }
</style>

<div class="teacher-participant-update">

    <h1 style="margin-bottom: 50px;"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>


    <?php

    echo $form->field($model, 'achievment')->textInput();

    ?>

    <?php

    echo $form->field($model, 'cert_number')->textInput();

    ?>

    <?= $form->field($model, 'date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control date_achieve',
            'autocomplete'=>'off'

        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]]) ?>

    <?php
    $checked = $model->winner == 1 ? 'checked' : '';
    echo '<div class="toggle-wrapper form-group field-participantachievementwork-winner">
                <input type="hidden" name="ParticipantAchievementWork[winner]" value="0">
                <input type="checkbox" value="1" id="participantachievementwork-winner" class="toggle-checkbox" name="ParticipantAchievementWork[winner]" '.$checked.'>
                <span class="toggle-icon off">Призер</span>
                <div class="toggle-container">
                    <div class="toggle-button"></div>
                </div>
                <span class="toggle-icon on">Победитель</span>
                <div class="help-block"></div>
           </div>';

    ?>

    <div class="form-group">
        <div class="button">

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>