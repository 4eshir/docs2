<?php

use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\ActiveForm;

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
    <?=$form->field($model, 'order_date')->widget(DatePicker::class, [
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
        ]])->label('Дата приказа'); ?>
    <?= $form->field($model, 'branch')->dropDownList(Yii::$app->branches->getList(), ['prompt' => '---'])->label('Отдел') ?>
    <?= $form->field($model, 'order_number')->dropDownList(Yii::$app->nomenclature->getList(), ['prompt' => '---'])->label('Код и описание номенклатуры') ?>
    <div class="training-group">
        <?php
        echo GridView::widget([
            'dataProvider' => $groups,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'group-selection',
                    'checkboxOptions' => function (\frontend\models\work\educational\training_group\TrainingGroupWork $model) {
                        return [
                            'class' => 'group-checkbox',
                            'data-id' => $model->id, // Добавляем ID группы для передачи в JS
                            'checked' => $model->open == 1,
                        ];
                    },
                ],
                'number',
                'start_date',
                'finish_date',
            ],
            'summaryOptions' => [
                'style' => 'display: none;', // Скрыть блок через CSS
            ],
        ]);
        ?>
    </div>
    <div class="training-group-participant">
        <?php
        echo GridView::widget([
            'dataProvider' => $groupParticipant,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'group-participant-selection',
                    'checkboxOptions' => function (\frontend\models\work\educational\training_group\TrainingGroupParticipantWork $model) {
                        return [
                            'class' => 'group-participant-checkbox' ,
                            'training-group-id' => $model->training_group_id,
                            'data-id' => $model->id, // Добавляем ID группы для передачи в JS
                        ];
                    },
                ],
                'training_group_id',
                'id'
            ],
            'rowOptions' => function ($model, $key, $index) {
                return ['id' => 'row-' . $model->id, 'class' => 'row-class-' . $index, 'name' => 'row-' . $model->training_group_id, 'style' => 'display: none;'];
            },
            'tableOptions' => [
                'class' => 'table table-striped table-bordered',
                'style' => 'position: relative;', // Необязательно, для кастомизации таблицы
            ],
            'headerRowOptions' => [
                'style' => 'display: none;', // Скрываем <thead>
            ],
            'summaryOptions' => [
                'style' => 'display: none;', // Скрыть блок через CSS
            ],
        ]);
        ?>
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
        const groupId = $(this).data('id');
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            var elements = document.getElementsByName('row-' + groupId);
            Array.from(elements).forEach(element => {
                element.style.display = 'block'; // Скрываем элемент
                console.log(element);
            });
        }
        else {
            var elements = document.getElementsByName('row-' + groupId);
            Array.from(elements).forEach(element => {
                element.style.display = 'none'; // Скрываем элемент
                console.log(element);
            });
        }
    });
");
?>
<script>
    window.onload = function () {
        var checkboxes = document.getElementsByClassName('group-checkbox');
        Array.from(checkboxes).forEach(function(checkbox) {
            if (checkbox.checked) {
                var elements = document.getElementsByName('row-' + checkbox.getAttribute('data-id'));
                Array.from(elements).forEach(element => {
                    element.style.display = 'block'; // Скрываем элемент
                    console.log(element);
                });
            }
        });
    };
</script>