<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Мероприятия';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить мероприятие', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php

    $gridColumns = [
        ['attribute' => 'name'],
        ['attribute' => 'start_date'],
        ['attribute' => 'finish_date'],
        ['attribute' => 'event_type_id', 'value' => function($model){
            return \app\models\work\EventTypeWork::find()->where(['id' => $model->event_type_id])->one()->name;
        }, 'filter' => [ 1 => "Соревновательный", 2 => "Несоревновательный"]],
        ['attribute' => 'address'],
        ['attribute' => 'eventLevelString', 'label' => 'Уровень мероприятия', 'value' => function($model){
            return \app\models\work\EventLevelWork::find()->where(['id' => $model->event_level_id])->one()->name;
        }, 'encodeLabel' => false],
        ['attribute' => 'scopesSplitter', 'label' => 'Тематическая направленность'],
        ['attribute' => 'childs', 'value' => function($model){
            return \app\models\work\EventParticipantsWork::find()->where(['event_id' => $model->id])->one()->child_participants;
        }, 'encodeLabel' => false],
        ['attribute' => 'childs_rst', 'value' => function($model){
            return \app\models\work\EventParticipantsWork::find()->where(['event_id' => $model->id])->one()->child_rst_participants;
        }, 'encodeLabel' => false],
        ['attribute' => 'teachers', 'value' => function($model){
            return \app\models\work\EventParticipantsWork::find()->where(['event_id' => $model->id])->one()->teacher_participants;
        }, 'encodeLabel' => false],
        ['attribute' => 'others', 'value' => function($model){
            return \app\models\work\EventParticipantsWork::find()->where(['event_id' => $model->id])->one()->other_participants;
        }, 'encodeLabel' => false],
        //['attribute' => 'participants_count'],
        ['attribute' => 'is_federal', 'value' => function($model){
            if ($model->is_federal == 1)
                return 'Да';
            else
                return 'Нет';
        }, 'filter' => [1 => "Да", 0 => "Нет"]],
        ['attribute' => 'responsibleString', 'label' => 'Ответственный(-ые) работник(-и)'],
        ['attribute' => 'eventDepartment', 'label' => 'Мероприятие проводит', 'value' => function($model){
            $tech = \app\models\work\EventBranchWork::find()->where(['branch_id' => 2])->andWhere(['event_id' => $model->id])->all();
            $quant = \app\models\work\EventBranchWork::find()->where(['branch_id' => 1])->andWhere(['event_id' => $model->id])->all();
            $cdntt = \app\models\work\EventBranchWork::find()->where(['branch_id' => 3])->andWhere(['event_id' => $model->id])->all();
            $mobquant = \app\models\work\EventBranchWork::find()->where(['branch_id' => 4])->andWhere(['event_id' => $model->id])->all();
            $cod = \app\models\work\EventBranchWork::find()->where(['branch_id' => 7])->andWhere(['event_id' => $model->id])->all();

            $result = '';
            if (count($tech) > 0)
                $result = $result.'Технопарк';
            if (count($quant) > 0)
                if ($result == '')
                    $result = $result.'Кванториум';
                else
                    $result = $result." Кванториум";
            if (count($cdntt) > 0)
                if ($result == '')
                    $result = $result.'ЦДНТТ';
                else
                    $result = $result." ЦДНТТ";
            if (count($mobquant) > 0)
                if ($result == '')
                    $result = $result.'Мобильный_кванториум';
                else
                    $result = $result." Мобильный_кванториум";

            if (count($cod) > 0)
                if ($result == '')
                    $result = $result.'ЦОД';
                else
                    $result = $result." ЦОД";

            return $result;
        }, 'format' => 'raw'],
        ['attribute' => 'orderString', 'value' => function($model){
            $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
            if ($order == null)
                return 'Нет';
            return Html::a('№'.$order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
        }, 'format' => 'raw', 'label' => 'Приказ'],
        'eventWayString',
        ['attribute' => 'regulationString', 'value' => function($model){
            $reg = \app\models\work\RegulationWork::find()->where(['id' => $model->regulation_id])->one();
            if ($reg == null)
                return 'Нет';
            return Html::a('Положение "'.$reg->name.'"', \yii\helpers\Url::to(['regulation/view', 'id' => $reg->id]));
        }, 'label' => 'Положение'],
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
            if ($data['errorsWork'] !== '')
                return ['class' => 'warning'];
        },
        'columns' => [
            ['attribute' => 'name'],
            ['attribute' => 'start_date'],
            ['attribute' => 'finish_date'],
            ['attribute' => 'event_type_id', 'value' => function($model){
                return \app\models\work\EventTypeWork::find()->where(['id' => $model->event_type_id])->one()->name;
            }, 'filter' => [ 1 => "Соревновательный", 2 => "Несоревновательный"]],
            ['attribute' => 'address'],
            ['attribute' => 'eventLevelString', 'label' => 'Уровень<br>мероприятия', 'value' => function($model){
                return \app\models\work\EventLevelWork::find()->where(['id' => $model->event_level_id])->one()->name;
            }, 'encodeLabel' => false],
            ['attribute' => 'participants_count'],
            ['attribute' => 'is_federal', 'value' => function($model){
                if ($model->is_federal == 1)
                    return 'Да';
                else
                    return 'Нет';
            }, 'filter' => [1 => "Да", 0 => "Нет"]],
            ['attribute' => 'responsibleString', 'label' => 'Ответственный(-ые) работник(-и)'],
            ['attribute' => 'orderString', 'value' => function($model){
                $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
                if ($order == null)
                    return 'Нет';
                return Html::a('№'.$order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
            }, 'format' => 'raw', 'label' => 'Приказ'],
            'eventWayString',
            ['attribute' => 'regulationString', 'value' => function($model){
                $reg = \app\models\work\RegulationWork::find()->where(['id' => $model->regulation_id])->one();
                if ($reg == null)
                    return 'Нет';
                return Html::a('Положение "'.$reg->name.'"', \yii\helpers\Url::to(['regulation/view', 'id' => $reg->id]));
            }, 'format' => 'raw', 'label' => 'Положение'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
