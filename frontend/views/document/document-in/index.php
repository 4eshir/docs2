<?php

use common\helpers\StringFormatter;
use frontend\models\work\document_in_out\DocumentInWork;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchDocumentIn */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Входящая документация';
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->session;
$tempArchive = $session->get("archiveIn");
$helper = new DocumentInWork();
?>
<div class="document-in-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $helper->createGroupButton(); ?>

    <p >

        <?php
        if ($tempArchive === null)
            echo Html::a('Показать архивные документы', ['document-in/index', 'archive' => 1, 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block;']);
        else
            echo Html::a('Скрыть архивные документы', ['document-in/index', 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block;']);
        ?>
    </p>

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?php

    $gridColumns = [
        ['attribute' => 'fullNumber'],
        ['attribute' => 'localDate', 'encodeLabel' => false],
        ['attribute' => 'realDate', 'encodeLabel' => false],
        ['attribute' => 'realNumber', 'encodeLabel' => false],

        ['attribute' => 'companyName', 'encodeLabel' => false],
        ['attribute' => 'documentTheme', 'encodeLabel' => false],
        ['attribute' => 'sendMethodName', 'value' => 'sendMethod.name'],
        ['attribute' => 'needAnswer', 'value' => function(DocumentInWork $model) {
            return $model->getNeedAnswer();
        }, 'format' => 'raw'],

    ];
    echo '<b>Скачать файл </b>';
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,

        'options' => [
            'padding-bottom: 100px',
        ]
    ]);

    ?>

    <div class="filter-toggle" id="filterToggle">
        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </div>

    <div class="filter-panel" id="filterPanel">
        <h2>Фильтры</h2>
        <label>
            <input type="checkbox"> Фильтр 1
        </label>
        <label>
            <input type="checkbox"> Фильтр 2
        </label>
        <label>
            <input type="checkbox"> Фильтр 3
        </label>
    </div>

    <div style="margin-bottom: 20px">

        <?php echo '<div style="margin-bottom: 10px; margin-top: 20px">'.Html::a('Показать просроченные документы', \yii\helpers\Url::to(['document-in/index', 'sort' => '1'])).
            ' || '.Html::a('Показать документы, требующие ответа', \yii\helpers\Url::to(['document-in/index', 'sort' => '2'])).
            ' || '.Html::a('Показать все документы', \yii\helpers\Url::to(['document-in/index'])).'</div>' ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
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
                    'value' => function(DocumentInWork $model){
                        return date('d.m.y', strtotime($model->local_date));
                    },
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
                    'value' => function(DocumentInWork $model) {
                        return date('d.m.y', strtotime($model->real_date));
                    },
                ],
                ['attribute' => 'realNumber', 'encodeLabel' => false],

                ['attribute' => 'companyName', 'encodeLabel' => false],
                ['attribute' => 'documentTheme', 'encodeLabel' => false],
                [
                    'attribute' => 'sendMethodName',
                    'filter' => Yii::$app->sendMethods->getList(),
                ],
                ['attribute' => 'needAnswer', 'value' => function(DocumentInWork $model) {
                    return $model->getNeedAnswer(StringFormatter::FORMAT_LINK);
                }, 'format' => 'raw'],

                //['class' => 'yii\grid\ActionColumn'],
                ['class' => \app\components\VerticalActionColumn::class],
            ],
        ]);

        ?>
    </div>
</div>
