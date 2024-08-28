<?php

use common\helpers\StringFormatter;
use common\models\work\document_in_out\DocumentOutWork;
use common\models\work\document_in_out\InOutDocumentsWork;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use kartik\grid\GridViewInterface;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchDocumentOut */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */
/* @var $model common\models\work\document_in_out\DocumentOutWork */
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
            'footer' => 'Низ окна',
        ]);
         $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'document_number')->textInput(['maxlength' => true])->label('Атрибут 1') ?>
        <?= $form->field($model, 'document_date')->textInput(['maxlength' => true])->label('Атрибут 2') ?>
        <?= $form->field($model, 'sent_date')->textInput(['maxlength' => true])->label('Атрибут 3') ?>
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

