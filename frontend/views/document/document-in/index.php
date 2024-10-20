<?php

use common\helpers\html\HtmlCreator;
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

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $helper->createGroupButton(); ?>

                <p hidden>

                    <?php
                    if ($tempArchive === null)
                        echo Html::a('Показать архивные документы', ['document-in/index', 'archive' => 1, 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block;']);
                    else
                        echo Html::a('Скрыть архивные документы', ['document-in/index', 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block;']);
                    ?>
                </p>

                <?= $this->render('_search', ['model' => $searchModel]) ?>

                <div class="export-menu">
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

                    echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,

                        'options' => [
                            'padding-bottom: 100px',
                        ],
                    ]);

                    ?>
                </div>
            </div>

            <?= HtmlCreator::filterToggle() ?>
        </div>
    </div>

    <?= $helper->createFilterPanel($searchModel) ?>

    <div style="margin-bottom: 20px">

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
            'rowOptions' => function ($model) {
                return ['data-href' => \yii\helpers\Url::to(['document/document-in/view', 'id' => $model->id])];
            },
        ]);

        ?>
    </div>
</div>
