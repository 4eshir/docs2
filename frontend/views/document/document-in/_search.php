<?php

///*$form->field($model, 'local_date')->widget(DatePicker::class, [
//        'dateFormat' => 'php:d.m.Y',
//        'language' => 'ru',
//        'options' => [
//            'placeholder' => 'Дата',
//            'class'=> 'form-control',
//            'autocomplete'=>'off'
//        ],
//        'clientOptions' => [
//            'changeMonth' => true,
//            'changeYear' => true,
//            'yearRange' => '2000:2100',
//        ]])->label('Дата поступления документа')*/

use common\helpers\html\HtmlBuilder;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchDocumentIn */

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php
    $searchFields = [
        'startDateSearch' => [
            'type' => 'date',
            'label' => 'Дата документа с',
            'placeholder' => 'Дата документа с',
            'dateFormat' => 'php:d.m.Y',
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2018:2030',
            ],
        ],
        'finishDateSearch' => [
            'type' => 'date',
            'label' => 'Дата документа по',
            'placeholder' => 'Дата документа по',
            'dateFormat' => 'php:d.m.Y',
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2018:2030',
            ],
        ],
        'number' => [
            'type' => 'text',
            'label' => 'Номер документа',
            'placeholder' => 'Номер документа',
        ],
        'documentTheme' => [
            'type' => 'text',
            'label' => 'Тема документа',
            'placeholder' => 'Тема документа',
        ],
        'keyWords' => [
            'type' => 'text',
            'label' => 'Ключевые слова',
            'placeholder' => 'Ключевые слова',
        ],
        'correspondentName' => [
            'type' => 'text',
            'label' => 'Корреспондент',
            'placeholder' => 'Корреспондент',
        ],
        'executorName' => [
            'type' => 'text',
            'label' => 'Исполнитель',
            'placeholder' => 'Исполнитель',
        ],
        'sendMethodName' => [
            'type' => 'dropdown',
            'label' => 'Способ получения',
            'data' => Yii::$app->sendMethods->getList(),
            'prompt' => 'Способ получения',
        ],
        'status' => [
            'type' => 'dropdown',
            'label' => 'Статус документа',
            'data' => Yii::$app->documentStatus->getList(),
            'prompt' => 'Статус документа',
        ],
    ];
    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::DOC_IN_INDEX); ?>

<?php ActiveForm::end(); ?>