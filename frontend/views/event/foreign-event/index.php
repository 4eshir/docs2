<?php

use frontend\models\work\event\ForeignEventWork;
use frontend\models\search\SearchForeignEvent;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel SearchForeignEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Учет достижений в мероприятиях';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="foreign-event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //echo Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <!--<div style="margin: 0 118%;">
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Желтый цвет - карточка учета достижений имеет ошибку">❔</div>
    </div>-->

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        ['attribute' => 'name'],
        ['attribute' => 'companyString'],
        ['attribute' => 'start_date'],
        ['attribute' => 'finish_date'],
        ['attribute' => 'city'],
        ['attribute' => 'eventWayString', 'value' => function(ForeignEventWork $model){
            return Yii::$app->eventWay->get($model->format);
        }],
        ['attribute' => 'eventLevelString', 'value' => function(ForeignEventWork $model){
            return Yii::$app->eventLevel->get($model->level);
        }],

        ['attribute' => 'teachersExport', 'contentOptions' => ['class' => 'text-nowrap'], 'value' => function(ForeignEventWork $model){
            return $model->getTeachers(ForeignEventWork::EXPORT_TYPE);
        }],

        ['attribute' => 'participantCount', 'format' => 'raw', 'label' => 'Кол-во участников', 'encodeLabel' => false],
        ['attribute' => 'winners', 'contentOptions' => ['class' => 'text-nowrap'], 'value' => function(ForeignEventWork $model){
            return $model->getWinners();
        }],
        ['attribute' => 'prizes', 'contentOptions' => ['class' => 'text-nowrap'], 'value' => function(ForeignEventWork $model){
            return $model->getPrizes();
        }],
        ['attribute' => 'businessTrips', 'value' => function(ForeignEventWork $model){
            return $model->isTrip();
        }],
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
        'columns' => [
            ['attribute' => 'name'],
            ['attribute' => 'companyString'],
            ['attribute' => 'start_date'],
            ['attribute' => 'finish_date'],
            ['attribute' => 'city'],
            ['attribute' => 'eventWayString', 'value' => function(ForeignEventWork $model){
                return Yii::$app->eventWay->get($model->format);
            }],
            ['attribute' => 'eventLevelString', 'value' => function(ForeignEventWork $model){
                return Yii::$app->eventLevel->get($model->level);
            }],

            ['attribute' => 'teachers', 'value' => function(ForeignEventWork $model){
                return $model->getTeachers(ForeignEventWork::VIEW_TYPE);
            }],

            ['attribute' => 'participantCount', 'format' => 'raw', 'label' => 'Кол-во участников', 'encodeLabel' => false],
            ['attribute' => 'winners', 'value' => function(ForeignEventWork $model){
                return $model->getWinners();
            }],
            ['attribute' => 'prizes', 'value' => function(ForeignEventWork $model){
                return $model->getPrizes();
            }],
            ['attribute' => 'businessTrips', 'value' => function(ForeignEventWork $model){
                return $model->isTrip();
            }],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
