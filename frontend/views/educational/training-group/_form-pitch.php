<?php

use frontend\forms\training_group\PitchGroupForm;
use frontend\models\work\educational\training_group\TrainingGroupExpertWork;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PitchGroupForm */
/* @var $peoples */

$this->title = "Редактировать учебную группу {$model->number}";
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Основная информация', Url::to(['educational/training-group/base-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Список учеников', Url::to(['educational/training-group/participant-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Расписание', Url::to(['educational/training-group/schedule-form', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Сведения о защите работ', Url::to(['educational/training-group/pitch-form', 'id' => $model->id]), ['class' => 'btn btn-success']) ?>

    <div class="training-group-schedule-form">
        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

        <?= $form->field($model, 'protectionDate')->widget(DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата',
                'class' => 'form-control',
                'autocomplete' => 'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]])->label('Дата выдачи сертификатов') ?>

        <div class="panel-body">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 10, // the maximum times, an element can be cloned (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $model->themes[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                    'name',
                    'project_type',
                    'description'
                ],
            ]); ?>

            <div class="container-items"><!-- widgetContainer -->
                <h3 class="panel-title pull-left">Темы проектов</h3>
                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                <?php foreach ($model->themes as $i => $theme): ?>
                    <div class="item panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <div class="pull-right">
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <?= $form->field($theme, "[{$i}]id")->hiddenInput()->label(false) ?>

                                <?= $form->field($theme, "[{$i}]name")->textInput()
                                    ->label('Тема проекта') ?>

                                <?= $form->field($theme, "[{$i}]project_type")->dropDownList(
                                    Yii::$app->projectType->getList(),
                                    ['prompt' => '---']
                                )->label('Тип проекта'); ?>

                                <?= $form->field($theme, "[{$i}]description")->textarea(
                                    ['rows' => 3]
                                )->label('Краткое описание проекта') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>

        <div class="panel-body">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items1', // required: css class selector
                'widgetItem' => '.item1', // required: css class
                'limit' => 10, // the maximum times, an element can be cloned (default 999)
                'min' => 0, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $model->experts[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'id',
                    'expertId',
                    'expert_type'
                ],
            ]); ?>

            <div class="container-items1"><!-- widgetContainer -->
                <h3 class="panel-title pull-left">Приглашенные эксперты</h3>
                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                <?php foreach ($model->experts as $i => $expert): ?>
                    <div class="item1 panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <div class="pull-right">
                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <?= $form->field($expert, "[{$i}]id")->hiddenInput()->label(false) ?>

                                <?= $form->field($expert, "[{$i}]expertId")->widget(Select2::classname(), [
                                    'data' => ArrayHelper::map($peoples, 'id', 'fullFio'),
                                    'size' => Select2::LARGE,
                                    'options' => ['prompt' => 'Выберите эксперта'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->label('ФИО эксперта'); ?>

                                <?= $form->field($expert, "[{$i}]expert_type")->dropDownList(
                                    [
                                        TrainingGroupExpertWork::TYPE_EXTERNAL => 'Внешний',
                                        TrainingGroupExpertWork::TYPE_INTERNAL => 'Внутренний'
                                    ],
                                )->label('Тип эксперта'); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
