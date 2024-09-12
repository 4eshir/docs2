<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchEvent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-search">

    <h5><b>Введите временные рамки для мероприятия</b></h5>
    <div class="col-xs-4" style="padding-left: 0; width: auto">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($model, 'start_date_search', ['template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('С') ?>
    </div>
    <div class="col-xs-4">
        <?= $form->field($model, 'finish_date_search', [ 'template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('По') ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <?php
    $branchs = \app\models\work\BranchWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
    $params = [
        'prompt' => '---'
    ];

    echo $form->field($model, 'eventBranchs')->dropDownList($items,$params)->label('Мероприятие проводит');

    ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
