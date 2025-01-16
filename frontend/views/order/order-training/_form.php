<?php

use app\models\work\order\OrderTrainingWork;
use common\helpers\DateFormatter;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model */
/* @var $people */
/* @var $scanFile */
/* @var $docFiles */
/* @var $groups */
/* @var $groupParticipant */
?>
<style>
    .bordered-div {
        border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
    }
</style>
<div class="order-training-form">
    <?php $form = ActiveForm::begin(); ?>
<?php if($model->id == NULL) {?>
    <?=
        $form->field($model, 'order_date')->widget(DatePicker::class, [
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
        ]])->label('Дата приказа');
    ?>
    <?=
        $form->field($model, 'branch')->dropDownList(
            Yii::$app->branches->getList(),
            [
                'prompt' => '---',
                'id' => 'branch-dropdown' // Добавляем id для доступа в JavaScript
            ]
        )->label('Отдел');
    ?>
    <?=
        // Выпадающий список для выбора кода и описания номенклатуры
        $form->field($model, 'order_number')->dropDownList(
            [], // Сначала оставляем его пустым
            [
                'prompt' => '---',
                'id' => 'order-number-dropdown' // Добавляем id для доступа в JavaScript
            ])->label('Код и описание номенклатуры');
        ?>
        <?php } else {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    ['label' => 'Дата приказа', 'attribute' => 'order_date', 'value' => function (OrderTrainingWork $model) {
                        return DateFormatter::format($model->order_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
                    }],
                    ['label' => 'Код и номенклатура приказа', 'value' => function (OrderTrainingWork $model) {
                        return $model->getOrderType();
                    }],
                    ['label' => 'Номер приказа', 'value' => function (OrderTrainingWork $model) {
                        return $model->getNumberPostfix();
                    }],
                    ['label' => 'Отдел', 'value' => function (OrderTrainingWork $model) {
                        return Yii::$app->branches->get($model->branch);
                    }],
                ],
            ]);
        }
    ?>

    <div class="training-group">
        <?= $this->render('_groups_grid', ['dataProvider' => $groups, 'model' => $model]) ?>
    </div>
    <div class="training-group-participant">
        <?= $this->render('_group-participant_grid', ['dataProvider' => $groupParticipant, 'model' => $model]) ?>
    </div>
    <?= $form->field($model, 'order_name')->textInput()->label('Наименование приказа') ;?>
    <div id="bring_id">
        <?php
        $params = [
            'id' => 'bring',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'bring_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Проект вносит');
        ?>
    </div>
    <div id="executor_id">
        <?php
        $params = [
            'id' => 'executor',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Кто исполняет');
        ?>
    </div>
    <div class = "bordered-div">
        <?= $form->field($model, "responsible_id")->widget(Select2::classname(), [
            'data' => ArrayHelper::map($people,'id','fullFio'),
            'size' => Select2::LARGE,
            'options' => [
                'prompt' => 'Выберите ответственного' ,
                'multiple' => true
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('ФИО ответственного'); ?>
    </div>
    <?= $form->field($model, 'key_words')->textInput()->label('Ключевые слова') ?>
    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан документа') ?>
    <?php if (strlen($scanFile) > 10): ?>
        <?= $scanFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

    <?php if (strlen($docFiles) > 10): ?>
        <?= $docFiles; ?>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-success',
            'onclick' => 'prepareAndSubmit();' // Подготовка скрытых полей перед отправкой
        ]) ?>

        <?php ActiveForm::end(); ?>
</div>
<?php
    $this->registerJs("
    $(document).on('change', '.group-checkbox', function () {
        const checkedCheckboxes = $('.group-checkbox:checked'); 
        const groupIds = [];
        checkedCheckboxes.each(function () {
            groupIds.push($(this).data('id')); // Собираем ID всех выбранных чекбоксов
        });  
        $.ajax({
            url: '" . Url::to(['get-group-participants-by-branch']) . "', // Укажите ваш правильный путь к контроллеру
            type: 'GET',
            data: { groupIds: JSON.stringify(groupIds) }, // Отправляем массив ID
            success: function (data) {
                var gridView = $('.training-group-participant .grid-view');
                gridView.html(data.gridHtml); // Обновляем HTML GridView
            },
            error: function() {
                alert('Ошибка при загрузке данных.');
            }
        });
    });");
?>
<?php
    $this->registerJs("
        $('#branch-dropdown').on('change', function() {
            var branchId = $(this).val();
            
            $.ajax({
                url: '" . Url::to(['order/order-training/get-list-by-branch']) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { branch_id: branchId },
                success: function(data) {
                    var options;
                    options = '<option value=\"\">---</option>';
                    $.each(data, function(index, value) {
                        options += '<option value=\"' + index + '\">' + value + '</option>';
                    });
                    $('#order-number-dropdown').html(options); // Обновляем второй выпадающий список
                }
            });
        });
    ");
?>
<?php
$this->registerJs("$('#branch-dropdown').on('change', function() {
    var branchId = $(this).val();
    $.ajax({
        url:'" . Url::to(['order/order-training/get-group-by-branch']) . "',
        type: 'GET',
        data: { branch: branchId },
        success: function(data) {
            var gridView = $('.training-group .grid-view');
            gridView.html(data.gridHtml); // Обновляем HTML GridView
        },
        error: function() {
            alert('Ошибка при загрузке данных.');
        }
    });
});");
$this->registerJs("
    window.onload = function () {
        const checkedCheckboxes = $('.group-checkbox:checked'); 
        const groupIds = [];
        checkedCheckboxes.each(function () {
            groupIds.push($(this).data('id')); // Собираем ID всех выбранных чекбоксов
        });  
        $.ajax({
            url: '" . Url::to(['get-group-participants-by-branch']) . "', // Укажите ваш правильный путь к контроллеру
            type: 'GET',
            data: { groupIds: JSON.stringify(groupIds) }, // Отправляем массив ID
            success: function (data) {
                var gridView = $('.training-group-participant .grid-view');
                gridView.html(data.gridHtml); // Обновляем HTML GridView
            },
            error: function() {
                alert('Ошибка при загрузке данных.');
            }
        });
    };
");
?>