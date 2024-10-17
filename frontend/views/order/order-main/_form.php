<?php

use app\components\DropDownDocument;
use app\components\DropDownResponsiblePeopleWidget;
use app\components\DynamicWidget;
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
/* @var $scanFile */
/* @var $docFiles */
/* @var $orders */
/* @var $regulations */
/* @var $modelResponsiblePeople */
/* @var $modelChangedDocuments */
?>
<style>
    .bordered-div {
        border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
    }
</style>
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

    <div id="archive-2" class="col-xs-4">
        <?= $form->field($model, 'order_number')->dropDownList(Yii::$app->nomenclature->getList(), ['prompt' => '---'])->label('Код и описание номенклатуры') ?>
    </div>
    <?= $form->field($model, 'archive')->checkbox(['id' => 'study_type', 'onchange' => 'checkArchive()']) ?>
    <div id="archive" class="col-xs-4"<?= $model->study_type == 0 ? 'hidden' : '' ?>>
        <?= $form->field($model, 'order_number')->textInput()->label('Архивный номер') ?>
    </div>
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

    <?php if (strlen($modelResponsiblePeople) > 10): ?>
        <?= $modelResponsiblePeople; ?>
    <?php endif; ?>


    <div class="bordered-div">
        <?php DynamicWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'model' => $model,
            'formId' => 'dynamic-form',
            'formFields' => ['order_name'],
        ]); ?>
        <div class="container-items">
            <h5 class="panel-title pull-left">Ответственные</h5><!-- widgetBody -->
            <div class="pull-right">
                <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
            </div>
            <div class="item panel panel-default" id = "item"><!-- widgetItem -->
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
                            ->field($model, 'names[]')
                            ->dropDownList(ArrayHelper::map($bringPeople, 'id', 'fullFio'), $params)
                            ->label('Ответственные');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        DynamicWidget::end()
        ?>
    </div>
    <?php if (strlen($modelChangedDocuments) > 10): ?>
        <?= $modelChangedDocuments; ?>
    <?php endif; ?>
    <div class="bordered-div">
        <?php DynamicWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'model' => $model,
            'formId' => 'dynamic-form',
            'formFields' => ['order_name'],
        ]); ?>

        <div class="container-items">
            <h5 class="panel-title pull-left">Изменение документов</h5><!-- widgetBody -->
            <div class="pull-right">
                <button type="button" class="add-item btn btn-success btn-xs" onclick = updateName()><span class="glyphicon glyphicon-plus"></span></button>
            </div>
            <div class="item panel panel-default" id = "item"><!-- widgetItem -->
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
                            ->label('Положение');
                        echo $form
                            ->field($model, 'status[]') // Используем обычный статус
                            ->dropDownList([
                                '1' => 'Утратило силу',
                                '2' => 'Изменено',
                            ], [
                                'prompt' => '---', // Подсказка для выбора
                            ])
                            ->label('Статус');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        DynamicWidget::end()
        ?>
    </div>
    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан документа') ?>
    <?php if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

    <?php if (strlen($docFiles) > 10): ?>
        <?= $docFiles; ?>
    <?php endif; ?>
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










