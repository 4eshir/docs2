<?php

use app\components\VerticalActionColumn;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use common\helpers\StringFormatter;
use frontend\helpers\document\DocumentOutHelper;
use frontend\models\work\document_in_out\DocumentOutWork;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchDocumentOut */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */
/* @var $peopleList */
$this->title = 'Исходящая документация';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="document-out-index">
    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= DocumentOutHelper::createGroupButton(); ?>

                <div class="export-menu">
                    <?php

                        $gridColumns = [
                            ['attribute' => 'fullNumber'],
                            ['attribute' => 'documentDate', 'encodeLabel' => false],
                            ['attribute' => 'sentDate', 'encodeLabel' => false],
                            ['attribute' => 'documentNumber', 'encodeLabel' => false],

                            ['attribute' => 'companyName', 'encodeLabel' => false],
                            ['attribute' => 'documentTheme', 'encodeLabel' => false],
                            ['attribute' => 'sendMethodName',
                                'value' => function(DocumentOutWork $model) {
                                    return Yii::$app->sendMethods->get($model->send_method);
                                }
                            ],
                            ['attribute' => 'isAnswer',
                                'value' => function(DocumentOutWork $model) {
                                    return $model->getIsAnswer();
                                },
                                'format' => 'raw'
                            ],
                        ];

                        echo ExportMenu::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => $gridColumns,

                            'options' => [
                                'padding-bottom: 100px',
                            ]
                        ]);

                    ?>
                </div>
            </div>

            <?= HtmlCreator::filterToggle() ?>
        </div>
    </div>

    <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 20px">

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => [
                ['attribute' => 'fullNumber'],
                ['attribute' => 'documentDate',
                    'value' => function(DocumentOutWork $model) {
                        return DateFormatter::format($model->document_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                    },
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],
                ['attribute' => 'documentTheme'],
                ['attribute' => 'companyName',
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],

                ['attribute' => 'executorName'],
                ['attribute' => 'sendMethodName',
                    'value' => function(DocumentOutWork $model) {
                        return Yii::$app->sendMethods->get($model->send_method);
                    }
                ],
                ['attribute' => 'sentDate',
                    'value' => function(DocumentOutWork $model) {
                        return DateFormatter::format($model->sent_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                    },
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],
                ['attribute' => 'isAnswer',
                    'value' => function(DocumentOutWork $model) {
                        return $model->getIsAnswer(StringFormatter::FORMAT_LINK);
                    },
                    'encodeLabel' => false,
                    'format' => 'raw',
                ],

                ['class' => VerticalActionColumn::class],
            ],
            'rowOptions' => function ($model) {
                return ['data-href' => Url::to([Yii::$app->frontUrls::DOC_OUT_VIEW, 'id' => $model->id])];
            },
        ]);

        ?>
    </div>


/*----------------------------------------------*/

        <?php
        Modal::begin([
            'toggleButton' => [
                'label' => 'Добавить резерв',
                'tag' => 'button',
                'class' => 'btn btn-success',
            ],
            'footer' => 'Модальное окно',
        ]);
         $form = ActiveForm::begin(); ?>
        <?php
        $params = [
            'prompt' => '------------',
            'onchange' => '
        $.post(
            "' . Url::toRoute('dependency-dropdown') . '", 
            {id: $(this).val()}, 
            function(res){
                var resArr = res.split("|split|");
                var elem = document.getElementsByClassName("pos");
                elem[0].innerHTML = resArr[0];
                elem = document.getElementsByClassName("com");
                elem[0].innerHTML = resArr[1];
            }
        );
    ',
        ];
        echo $form
            ->field($model, 'executor_id')
            ->dropDownList(ArrayHelper::map($peopleList, 'id','fullFio'), $params)
            ->label('Кто исполнил');
        ?>
        <?= $form->field($model, 'document_date')->widget(DatePicker::class, [
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
            ]])->label('Дата документа') ?>
    <div class="form-group">
        <?= Html::submitButton('Создать резерв', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end();
        Modal::end();
        ?>

</div>

