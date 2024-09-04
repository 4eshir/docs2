<?php

use common\helpers\StringFormatter;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\document_in_out\InOutDocumentsWork;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use kartik\grid\GridViewInterface;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchDocumentIn */
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
        <?= Html::a('Добавить приказ об участии', ['create'], ['class' => 'btn btn-info', 'style' => 'display: inline-block;']) ?>
        <?= Html::a('Добавить резерв', ['reserve'], ['class' => 'btn btn-secondary','style' => 'display: inline-block;',]) ?>
    </p>

    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => false,

            'columns' => [
                ['attribute' => 'fullNumber'],
                [
                    'attribute' => 'localDate',
                    'filter' => DateRangePicker::widget([
                        'language' => 'ru',
                        'model' => $searchModel,
                        'attribute' => 'localDate',
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
                [
                    'attribute' => 'realDate',
                    'filter' => DateRangePicker::widget([
                        'language' => 'ru',
                        'model' => $searchModel,
                        'attribute' => 'realDate',
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
                    'encodeLabel' => false,
                    'value' => NULL,
                ],
                ['attribute' => 'realNumber', 'encodeLabel' => false],

                ['attribute' => 'companyName', 'encodeLabel' => false],
                ['attribute' => 'documentTheme', 'encodeLabel' => false],
                [
                    'attribute' => 'sendMethodName',
                    'filter' => Yii::$app->sendMethods->getList(),
                ],
                ['attribute' => 'needAnswer', 'value' => NULL, 'format' => 'raw'],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);?>

</div>
