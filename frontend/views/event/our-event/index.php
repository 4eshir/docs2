<?php

use frontend\models\search\SearchEvent;
use frontend\models\work\event\EventWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SearchEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мероприятия';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить мероприятие', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php /*echo $this->render('_search', ['model' => $searchModel]); */?>


    <?php

    $gridColumns = [
        ['attribute' => 'name'],
        ['attribute' => 'start_date'],
        ['attribute' => 'finish_date'],
        ['attribute' => 'event_type', 'value' => function(EventWork $model){
            return Yii::$app->eventType->get($model->event_type);
        }, 'filter' => Yii::$app->eventType->getList()],
        ['attribute' => 'address'],
        ['attribute' => 'event_level', 'label' => 'Уровень мероприятия', 'value' => function(EventWork $model){
            return Yii::$app->eventLevel->get($model->event_level);
        }, 'encodeLabel' => false],
        ['attribute' => 'scopesSplitter', 'label' => 'Тематическая направленность'],
        ['attribute' => 'child_participants_count', 'value' => function(EventWork $model){
            return $model->child_participants_count;
        }, 'encodeLabel' => false],
        ['attribute' => 'child_rst_participants_count', 'value' => function(EventWork $model){
            return $model->child_rst_participants_count;
        }, 'encodeLabel' => false],
        ['attribute' => 'teacher_participants_count', 'value' => function(EventWork $model){
            return $model->teacher_participants_count;
        }, 'encodeLabel' => false],
        ['attribute' => 'other_participants_count', 'value' => function(EventWork $model){
            return $model->other_participants_count;
        }, 'encodeLabel' => false],
        //['attribute' => 'participants_count'],
        ['attribute' => 'is_federal', 'value' => function(EventWork $model){
            if ($model->is_federal == 1) {
                return 'Да';
            }
            else{
                return 'Нет';
            }
        }, 'filter' => [1 => "Да", 0 => "Нет"]],
        ['attribute' => 'responsibleString', 'label' => 'Ответственный(-ые) работник(-и)'],
        ['attribute' => 'eventBranches', 'label' => 'Мероприятие проводит', 'format' => 'raw'],
        ['attribute' => 'orderString', 'value' => function(EventWork $model){
            /*$order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
            if ($order == null)
                return 'Нет';
            return Html::a('№'.$order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));*/
            return 'Coming soon';
        }, 'format' => 'raw', 'label' => 'Приказ'],
        'eventWayString',
        ['attribute' => 'regulationRaw', 'label' => 'Положение', 'format' => 'raw'],
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
    <div style="margin-bottom: 10px">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
        },
        'columns' => [
            ['attribute' => 'name'],
            ['attribute' => 'start_date'],
            ['attribute' => 'finish_date'],
            ['attribute' => 'event_type', 'value' => function(EventWork $model){
                return Yii::$app->eventType->get($model->event_type);
            }, 'filter' => Yii::$app->eventType->getList()],
            ['attribute' => 'address'],
            ['attribute' => 'event_level', 'label' => 'Уровень мероприятия', 'value' => function(EventWork $model){
                return Yii::$app->eventLevel->get($model->event_level);
            }, 'encodeLabel' => false],
            ['attribute' => 'scopesSplitter', 'label' => 'Тематическая направленность'],
            ['attribute' => 'child_participants_count', 'value' => function(EventWork $model){
                return $model->child_participants_count;
            }, 'encodeLabel' => false],
            ['attribute' => 'child_rst_participants_count', 'value' => function(EventWork $model){
                return $model->child_rst_participants_count;
            }, 'encodeLabel' => false],
            ['attribute' => 'teacher_participants_count', 'value' => function(EventWork $model){
                return $model->teacher_participants_count;
            }, 'encodeLabel' => false],
            ['attribute' => 'other_participants_count', 'value' => function(EventWork $model){
                return $model->other_participants_count;
            }, 'encodeLabel' => false],
            //['attribute' => 'participants_count'],
            ['attribute' => 'is_federal', 'value' => function(EventWork $model){
                if ($model->is_federal == 1) {
                    return 'Да';
                }
                else{
                    return 'Нет';
                }
            }, 'filter' => [1 => "Да", 0 => "Нет"]],
            ['attribute' => 'responsibleString', 'label' => 'Ответственный(-ые) работник(-и)'],
            ['attribute' => 'eventBranches', 'label' => 'Мероприятие проводит', 'format' => 'raw'],
            ['attribute' => 'orderString', 'value' => function(EventWork $model){
                /*$order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
                if ($order == null)
                    return 'Нет';
                return Html::a('№'.$order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));*/
                return 'Coming soon';
            }, 'format' => 'raw', 'label' => 'Приказ'],
            'eventWayString',
            ['attribute' => 'regulationRaw', 'label' => 'Положение', 'format' => 'raw'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
