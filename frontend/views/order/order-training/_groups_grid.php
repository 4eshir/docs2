<?php
/* @var $dataProvider */
/* @var $model */
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\grid\GridView;
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'name' => 'group-selection',
            'checkboxOptions' => function (TrainingGroupWork $group) use ($model) {
                return [
                    'class' => 'group-checkbox',
                    'data-id' => $group->id,
                    'checked' => 1,
                ];
            },
        ],
        'number',
        'start_date',
        'finish_date',
    ],
    'summaryOptions' => [
        'style' => 'display: none;',
    ],
]);
?>