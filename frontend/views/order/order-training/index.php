<?php

use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchOrderEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приказы по образовательной деятельности';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
$tempArchive = $session->get("archiveIn");
?>
<div class="order-training-index">
    <h1><?= Html::encode($this->title) ?></h1><p>
        <?= Html::a('Добавить приказ по образовательной деятельности', ['create'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']) ?>
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
