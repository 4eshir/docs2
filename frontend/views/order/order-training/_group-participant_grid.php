<?php

use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\grid\GridView;
/* @var $dataProvider */
/* @var $model */
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'name' => 'group-participant-selection',
            'checkboxOptions' => function (TrainingGroupParticipantWork $participant) use ($model) {
                return [
                    'class' => 'group-participant-checkbox' ,
                    'training-group-id' => $participant->training_group_id,
                    'data-id' => $participant->id, // Добавляем ID группы для передачи в JS
                    'checked' => $participant->getOrderTrainingGroupParticipantRelation($model->id) == 1,
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
?>