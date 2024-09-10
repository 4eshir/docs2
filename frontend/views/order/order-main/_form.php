<?php


use app\components\DropDownDocument;
use app\components\DropDownResponsiblePeopleWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\work\order\OrderMainWork */
/* @var $form yii\widgets\ActiveForm */
/* @var $bringPeople */
?>
<div class="order-main-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'order_date')->widget(DatePicker::class, [
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
            'yearRange' => '2000:2100',
        ]])->label('Дата приказа') ?>
    <?= $form->field($model, 'order_number')->textInput()->label('Код и описание номенклатуры') ?>
    <?= $form->field($model, 'order_name')->textInput()->label('Наименование приказа') ?>


    <div id="bring">
        <?php
        $params = [
            'id' => 'bring',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'bring_id')
            ->dropDownList(ArrayHelper::map($bringPeople, 'id', 'fullFio'), $params)
            ->label('Проект вносит');
        ?>
    </div>
    <div id="executor">
        <?php
        $params = [
            'id' => 'executor',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id')
            ->dropDownList(ArrayHelper::map($bringPeople, 'id', 'fullFio'), $params)
            ->label('Кто исполняет');
        ?>

    </div>
    <?
    echo DropDownResponsiblePeopleWidget::widget([
    'model' => $model,
    'bringPeople' => $bringPeople,
    'form' => $form,
    ]);
    ?>
    <div>
        Изменение документов
    </div>
    <?
    echo DropDownDocument::widget([
        'model' => $model,
        'bringPeople' => $bringPeople,
        'form' => $form,
    ]);
    ?>
    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end(); ?>



</div>













