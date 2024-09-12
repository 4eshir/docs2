<?php

use app\components\DropDownPosition;
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

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-briefcase"></i> Должности</h4></div>
        </div>
    </div>
    <?php
    $params = [
        'prompt' => '---',
        'id' => 'org'
    ];
    echo DropDownPosition::widget([
        'model' => $model,
        'positions' => $positions,
        'form' => $form,
        'branches' => $branches
    ]);
    echo $form->field($model, 'company_id')->dropDownList(ArrayHelper::map($companies, 'id', 'name'), $params)->label('Организация');
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