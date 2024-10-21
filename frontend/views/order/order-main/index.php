<?php

use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchDocumentIn */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приказы по осн. деятельности';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
$tempArchive = $session->get("archiveIn");
?>
<div class="order-main-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Добавить приказ по основной деятельности', ['create'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']) ?>
        <?= Html::a('Добавить образовательный приказ', ['create'], ['class' => 'btn btn-warning', 'style' => 'display: inline-block;']) ?>
        <?= Html::a('Добавить приказ об участии', ['order/order-event/create'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']) ?>
        <?= Html::a('Добавить резерв', ['reserve'], ['class' => 'btn btn-secondary','style' => 'display: inline-block;',]) ?>
    </p>
    <?php
    $gridColumns = [
    ['attribute' => 'fullNumber'],
    ['attribute' => 'orderDate', 'encodeLabel' => false],
    ['attribute' => 'orderName', 'encodeLabel' => false],
    ['attribute' => 'bringName', 'encodeLabel' => false],
    ['attribute' => 'creatorName', 'encodeLabel' => false],
    ['attribute' => 'state', 'encodeLabel' => false],
     'format' => 'raw'
    ];
    ?>
    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => false,

            'columns' => [
                ['attribute' => 'fullNumber'],
                [
                    'attribute' => 'orderDate',
                    'filter' => DateRangePicker::widget([
                        'language' => 'ru',
                        'model' => $searchModel,
                        'attribute' => 'orderDate',
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'timePicker' => false,
                            'timePickerIncrement' => 365,
                            'locale' => [
                                'format' => 'd.m.y',
                                'cancelLabel' => 'Закрыть',
                                'applyLabel' => 'Найти',
                            ]
                        ]
                    ]),
                    'value' => NULL,
                    'encodeLabel' => false,
                ],
                ['attribute' => 'orderName', 'encodeLabel' => false],
                ['attribute' => 'bringName', 'encodeLabel' => false],
                ['attribute' => 'executorName', 'encodeLabel' => false],
                ['attribute' => 'state', 'encodeLabel' => false],
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);?>

</div>
