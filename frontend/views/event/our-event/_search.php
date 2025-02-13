<?php


/* @var $searchModel \frontend\models\search\SearchEvent */

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use yii\widgets\ActiveForm;

//Yii::$app->eventType->getList()
?>

<div class="event-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'], // Действие контроллера для обработки поиска
        'method' => 'get', // Метод GET для передачи параметров в URL
        'options' => ['data-pjax' => true], // Для использования Pjax
    ]); ?>

    <?php
    $searchFields = array_merge(
        SearchFieldHelper::dateField('startDateSearch', 'Дата мероприятия с', 'Дата мероприятия с'),
        SearchFieldHelper::dateField('finishDateSearch', 'Дата мероприятия по', 'Дата мероприятия по'),
        SearchFieldHelper::textField('eventName', 'Наименование мероприятия', 'Наименование мероприятия'),
        SearchFieldHelper::dropdownField('eventWay', 'Формат проведения', Yii::$app->eventWay->getList(), 'Формат проведения'),
        SearchFieldHelper::dropdownField('eventType', 'Тип мероприятия', Yii::$app->eventType->getList(), 'Тип мероприятия'),
        SearchFieldHelper::dropdownField('eventLevel', 'Уровень мероприятия', Yii::$app->eventLevel->getList(), 'Уровень мероприятия'),
        SearchFieldHelper::dropdownField('eventForm', 'Форма мероприятия', Yii::$app->eventForm->getList(), 'Форма мероприятия'),
        SearchFieldHelper::dropdownField('eventScope', 'Сферы участия', Yii::$app->participationScope->getList(), 'Сферы участия'),
    );

    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::OUR_EVENT_INDEX); ?>

    <?php ActiveForm::end(); ?>

</div>
