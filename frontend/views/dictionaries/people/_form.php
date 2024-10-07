<?php

use app\components\DropDownPosition;
use app\components\DynamicWidget;
use common\components\dictionaries\base\BranchDictionary;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $companies CompanyWork[] */
/* @var $form yii\widgets\ActiveForm */
/* @var $positions PositionWork */
/* @var $branches */
?>

<div class="people-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true])->label('Фамилия') ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true])->label('Имя') ?>

    <?= $form->field($model, 'patronymic')->textInput(['maxlength' => true])->label('Отчество') ?>

    <?php DynamicWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper',
        'widgetBody' => '.container-items',
        'widgetItem' => '.item',
        'model' => $model,
        'formId' => 'dynamic-form',
        'formFields' => ['order_name'],
    ]); ?>

    <div class="container-items">
        <h5 class="panel-title pull-left">Должности и организации</h5>
        <div class="pull-right">
            <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
        </div>
        <div class="item panel panel-default" id = "item">
            <button type="button" class="remove-item btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus"></span></button>
            <div class="panel-heading">
                <div class="clearfix"></div>
            </div>
            <div class = "form-label">
                <div class="panel-body">
                    <?php
                    $params = [
                        'id' => 'names',
                        'class' => 'form-control pos',
                        'prompt' => '---',
                    ];
                    echo $form
                        ->field($model, 'orders[]')
                        ->dropDownList(ArrayHelper::map($orders, 'id', 'orderName'), $params)
                        ->label('Приказ');
                    echo $form
                        ->field($model, 'regulations[]')
                        ->dropDownList(ArrayHelper::map($regulations, 'id', 'name'), $params)
                        ->label('Приказ');
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    DynamicWidget::end()
    ?>

    <div id="orghid" <?= !$model->inMainCompany() ? 'hidden' : '' ?>>

        <?= $form->field($model, 'short')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'genitive_surname')->textInput(['maxlength' => true])->label('Фамилия в обороте "назначить <i>кого</i>"') ?>
        <?= $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList(), ['prompt' => '---']); ?>
        <?= $form->field($model, 'birthdate')->widget(DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '1900:2100',
            ]]) ?>

        <?= $form->field($model, 'sex')->radioList(array(
                0 => 'Мужской',
                1 => 'Женский',
                2 => 'Другое'
        ), ['value' => $model->sex, 'class' => 'i-checks']) ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $("#org").change(function() {
        if (this.options[this.selectedIndex].value === `1`)
            $("#orghid").removeAttr("hidden");
        else
            $("#orghid").attr("hidden", "true");
    });
</script>