<?php

use common\helpers\StringFormatter;
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

$session = Yii::$app->session;
$tempArchive = $session->get("archiveIn");
?>
<div class="document-in-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
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

        <?= Html::a('Добавить исходящий документ', ['create'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']) ?>
        <?= Html::a('Добавить резерв', ['document-out/create-reserve'], ['class' => 'btn btn-warning', 'style' => 'display: inline-block;']) ?>
        <?php
        if ($tempArchive === null)
            echo Html::a('Показать архивные документы', ['document-out/index', 'archive' => 1, 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block; background-color: #ededed']);
        else
            echo Html::a('Скрыть архивные документы', ['document-out/index', 'type' => 'button'], ['class' => 'btn btn-secondary', 'style' => 'display: inline-block; background-color: #ededed']);
        ?>
    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?php

    $gridColumns = [
        ['attribute' => 'fullNumber'],
        ['attribute' => 'documentDate', 'encodeLabel' => false],
        ['attribute' => 'sentDate', 'encodeLabel' => false],
        ['attribute' => 'documentNumber', 'encodeLabel' => false],
        ['attribute' => 'companyName', 'encodeLabel' => false],
        ['attribute' => 'documentTheme', 'encodeLabel' => false],
        ['attribute' => 'sendMethodName', 'value' => 'sendMethod.name'],
        ['attribute' => 'isAnswer', 'value' => function(DocumentOutWork $model) {
            return $model->getIsAnswer();
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
    <div style="margin-bottom: 20px">

        <?php echo '<div style="margin-bottom: 10px; margin-top: 20px">'.Html::a('Показать просроченные документы', \yii\helpers\Url::to(['document-out/index', 'sort' => '1'])).
            ' || '.Html::a('Показать документы, требующие ответа', \yii\helpers\Url::to(['document-out/index', 'sort' => '2'])).
            ' || '.Html::a('Показать все документы', \yii\helpers\Url::to(['document-out/index'])).'</div>' ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => false,
            'columns' => [
                ['attribute' => 'fullNumber'],
                [
                    'attribute' => 'documentDate',
                    'filter' => DateRangePicker::widget([
                        'language' => 'ru',
                        'model' => $searchModel,
                        'attribute' => 'documentDate',
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
                    'value' => function(DocumentOutWork $model) {
                        return date('d.m.y', strtotime($model->document_date));
                    },
                    'encodeLabel' => false,
                ],

                [
                    'attribute' => 'sentDate',
                    'filter' => DateRangePicker::widget([
                        'language' => 'ru',
                        'model' => $searchModel,
                        'attribute' => 'sentDate',
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
                    'value' => function(DocumentOutWork $model) {
                        return date('d.m.y', strtotime($model->sent_date));
                    },
                ],
                ['attribute' => 'documentNumber',
                    'encodeLabel' => false],
                ['attribute' => 'companyName',
                    'encodeLabel' => false],
                ['attribute' => 'documentTheme',
                    'encodeLabel' => false],
                [
                    'attribute' => 'sendMethodName',
                    'filter' => Yii::$app->sendMethods->getList(),
                ],
                ['attribute' => 'isAnswer', 'value' => function(DocumentOutWork $model) {
                    return $model->getIsAnswer(StringFormatter::FORMAT_LINK);
                }, 'format' => 'raw'],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>

