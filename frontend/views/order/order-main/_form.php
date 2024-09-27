<?php

use app\components\DropDownDocument;
use app\components\DropDownResponsiblePeopleWidget;
use app\models\work\order\OrderMainWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model OrderMainWork */
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

    <div id="archive" class="col-xs-4"<?= $model->study_type == 0 ? 'hidden' : '' ?>>
        <?= $form->field($model, 'order_number')->textInput()->label('Архивный номер') ?>
    </div>
    <div id="archive-2" class="col-xs-4">
        <?= $form->field($model, 'order_number')->textInput()->label('Код и описание номенклатуры') ?>
    </div>
    <?= $form->field($model, 'archive')->checkbox(['id' => 'study_type', 'onchange' => 'checkArchive()']) ?>
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


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    function checkArchive() {
        var chkBox = document.getElementById('study_type'); // Получаем чекбокс по ID
        // Если чекбокс отмечен
        if (chkBox.checked) {
            // Показываем элемент, убирая атрибут hidden
            $("#archive").prop("hidden", false);
            $("#archive-2").prop("hidden", true);
        } else {
            // Скрываем элемент, добавляя атрибут hidden
            $("#archive").prop("hidden", true);
            $("#archive-2").prop("hidden", false);
        }
    }
</script>










