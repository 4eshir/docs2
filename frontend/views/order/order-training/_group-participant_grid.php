<?php

use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $dataProvider */
/* @var $model */
/* @var $nomenclature */
/* @var $groups */

if($nomenclature != '11-31') {
    // зачисление и отчисление
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'group-participant-selection',
                'checkboxOptions' => function (TrainingGroupParticipantWork $participant) use ($model) {
                    return [
                        'class' => 'group-participant-checkbox',
                        'training-group-id' => $participant->training_group_id,
                        'data-id' => $participant->id, // Добавляем ID группы для передачи в JS
                        'checked' => 1
                    ];
                },
            ],
            'training_group_id',
            'fullFio',
            'id'
        ],
        'rowOptions' => function ($model, $key, $index) {
            return ['id' => 'row-' . $model->id, 'class' => 'row-class-' . $index, 'name' => 'row-' . $model->training_group_id];
        },
        'tableOptions' => [
            'class' => 'table table-striped table-bordered',
            'style' => 'position: relative;', // Необязательно, для кастомизации таблицы
        ],
        'summaryOptions' => [
            'style' => 'display: none;', // Скрыть блок через CSS
        ],
    ]);
}
else {
    //перевод
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'group-participant-selection',
                'checkboxOptions' => function (TrainingGroupParticipantWork $participant) use ($model) {
                    return [
                        'class' => 'group-participant-checkbox',
                        'training-group-id' => $participant->training_group_id,
                        'data-id' => $participant->id, // Добавляем ID группы для передачи в JS
                        'checked' => NULL,
                    ];
                },
            ],
            'fullFio',
            [
                'attribute' => 'dropdownField', // Условное имя атрибута
                'format' => 'raw', // Чтобы отобразить HTML-код
                'label' => 'Список групп',
                'value' => function (TrainingGroupParticipantWork $participant) use ($groups, $model) {
                    // Формируем HTML-код выпадающего списка
                    return Html::dropDownList(
                        'transfer-group', // Имя элемента
                        $participant->training_group_id, // Значение по умолчанию
                        ArrayHelper::map($groups, 'id', 'number'),
                        [
                            'class' => 'form-control', // CSS-класс
                            'data-id' => $participant->id, // Пользовательские атрибуты
                        ]
                    );
                },
            ],
        ],
        'rowOptions' => function ($model, $key, $index) {
            return ['id' => 'row-' . $model->id, 'class' => 'row-class-' . $index, 'name' => 'row-' . $model->training_group_id];
        },
        'tableOptions' => [
            'class' => 'table table-striped table-bordered',
            'style' => 'position: relative;', // Необязательно, для кастомизации таблицы
        ],
        'summaryOptions' => [
            'style' => 'display: none;', // Скрыть блок через CSS
        ],
    ]);
}
?>