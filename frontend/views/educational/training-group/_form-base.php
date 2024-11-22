<?php

use app\components\DynamicWidget;
use frontend\forms\training_group\TrainingGroupBaseForm;
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use unclead\multipleinput\MultipleInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrainingGroupBaseForm */
/* @var $modelTeachers */
/* @var $trainingPrograms */
/* @var $people */
/* @var $form ActiveForm */

?>

<div class="training-group-base-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList())->label('Отдел, производящий учет') ?>
    <?= $form->field($model, 'trainingProgramId')->widget(Select2::classname(), [
        'data' => ArrayHelper::map($trainingPrograms,'id','name'),
        'size' => Select2::LARGE,
        'options' => ['prompt' => 'Выберите образовательную программу'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('Образовательная программа'); ?>
    <?= $form->field($model, 'budget')->checkbox() ?>
    <?= $form->field($model, 'network')->checkbox() ?>

    <div class="panel-body">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
            'widgetBody' => '.container-items', // required: css class selector
            'widgetItem' => '.item', // required: css class
            'limit' => 4, // the maximum times, an element can be cloned (default 999)
            'min' => 1, // 0 or 1 (default 1)
            'insertButton' => '.add-item', // css class
            'deleteButton' => '.remove-item', // css class
            'model' => $modelTeachers[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'full_name',
            ],
        ]); ?>

        <div class="container-items"><!-- widgetContainer -->
            <?php foreach ($modelTeachers as $i => $modelTeacher): ?>
                <div class="item panel panel-default"><!-- widgetBody -->
                    <div class="panel-heading">
                        <h3 class="panel-title pull-left">ФИО педагога</h3>
                        <div class="pull-right">
                            <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <?= $form->field($modelTeacher, "[{$i}]id")->widget(Select2::classname(), [
                                'data' => ArrayHelper::map($people,'id','fullFio'),
                                'size' => Select2::LARGE,
                                'options' => ['prompt' => 'Выберите преподавателя'],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])->label('ФИО'); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>

    <?= $form->field($model, 'startDate')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата начала занятий',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]])->label('Дата начала занятий') ?>

    <?= $form->field($model, 'endDate')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата окончания занятий',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]])->label('Дата окончания занятий') ?>
    <?= $form->field($model, 'endLoadOrders')->checkbox() ?>

    <?= $form->field($model, 'photos')->fileInput(['multiple' => true])->label('Фотоматериалы')?>
    <?= $form->field($model, 'presentations')->fileInput(['multiple' => true])->label('Презентационные материалы')?>
    <?= $form->field($model, 'workMaterials')->fileInput(['multiple' => true])->label('Рабочие материалы')?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>