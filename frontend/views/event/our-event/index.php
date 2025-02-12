<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchEvent;
use frontend\models\work\event\EventWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Мероприятия';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
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

    <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 10px">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,

        'columns' => [
            ['attribute' => 'name', 'encodeLabel' => false],
            ['attribute' => 'datePeriod', 'encodeLabel' => false],
            ['attribute' => 'eventLevelAndType', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'address', 'encodeLabel' => false],
            ['attribute' => 'participantCount', 'encodeLabel' => false],
            ['attribute' => 'orderName', 'encodeLabel' => false],
            ['attribute' => 'eventWay', 'encodeLabel' => false],
            ['attribute' => 'regulationRaw', 'encodeLabel' => false, 'format' => 'raw'],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::OUR_EVENT_VIEW, 'id' => $model->id])];
        },
    ]); ?>
    </div>
</div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>