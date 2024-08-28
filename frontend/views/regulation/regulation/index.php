<?php

use common\helpers\StringFormatter;
use common\models\work\document_in_out\DocumentInWork;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use kartik\grid\GridViewInterface;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SearchRegulation */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Положения, инструкции, правила';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="document-in-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить положение', ['create'], ['class' => 'btn btn-success', 'style' => 'display: inline-block;']) ?>
    </p>

    <?php

    $gridColumns = [
        ['attribute' => 'date', 'label' => 'Дата положения'],
        ['attribute' => 'name'],
        ['attribute' => 'orderString', 'label' => 'Приказ', 'value' => function($model){
            /*$order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
            $doc_num = 0;
            if ($order->order_postfix == null)
                $doc_num = $order->order_number.'/'.$order->order_copy_id;
            else
                $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
            return 'Приказ №'.$doc_num.' "'.$order->order_name.'"';*/
            return 'Coming soon';
        }],
        ['attribute' => 'ped_council_number', 'label' => '№ пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
        ['attribute' => 'ped_council_date', 'label' => 'Дата пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
        ['attribute' => 'par_council_number', 'label' => '№ совета<br>род.', 'encodeLabel' => false, 'format' => 'raw'],
        ['attribute' => 'par_council_date', 'label' => 'Дата совета<br>род.', 'encodeLabel' => false, 'format' => 'raw'],
        ['attribute' => 'state', 'label' => 'Состояние', 'value' => function($model){
            /*if ($model->state == 1)
                return 'Актуально';
            else
            {
                $exp = \app\models\work\ExpireWork::find()->where(['expire_order_id' => $model->order_id])->one();
                if ($exp == null)
                    $exp = \app\models\work\ExpireWork::find()->where(['expire_regulation_id' => $model->id])->one();
                $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $exp->active_regulation_id])->one();
                $doc_num = 0;

                if ($order->order_postfix == null)
                    $doc_num = $order->order_number.'/'.$order->order_copy_id;
                else
                    $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                return 'Утратило силу в связи с приказом №'.$doc_num;
            }*/
            return 'Coming soon';
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

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function($data) {
                if ($data['state'] == 0)
                    return ['style' => 'background: #c0c0c0']; //return ['class' => 'danger'];
                else
                    return ['class' => 'default'];
            },
            'columns' => [

                ['attribute' => 'date', 'label' => 'Дата положения'],
                ['attribute' => 'name'],
                ['attribute' => 'orderString', 'label' => 'Приказ', 'value' => function($model){
                    /*$order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
                    $doc_num = 0;
                    if ($order->order_postfix == null)
                        $doc_num = $order->order_number.'/'.$order->order_copy_id;
                    else
                        $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                    return 'Приказ №'.$doc_num.' "'.$order->order_name.'"';*/
                    return 'Coming soon';
                }],
                ['attribute' => 'ped_council_number', 'label' => '№ пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
                ['attribute' => 'ped_council_date', 'label' => 'Дата пед.<br>совета', 'encodeLabel' => false, 'format' => 'raw'],
                ['attribute' => 'par_council_number', 'label' => '№ совета<br>род.', 'encodeLabel' => false, 'format' => 'raw'],
                ['attribute' => 'par_council_date', 'label' => 'Дата совета<br>род.', 'encodeLabel' => false, 'format' => 'raw'],
                ['attribute' => 'state', 'label' => 'Состояние', 'value' => function($model){
                    /*if ($model->state == 1)
                        return 'Актуально';
                    else
                    {
                        $exp = \app\models\work\ExpireWork::find()->where(['expire_order_id' => $model->order_id])->one();
                        if ($exp == null)
                            $exp = \app\models\work\ExpireWork::find()->where(['expire_regulation_id' => $model->id])->one();
                        $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $exp->active_regulation_id])->one();
                        $doc_num = 0;

                        if ($order->order_postfix == null)
                            $doc_num = $order->order_number.'/'.$order->order_copy_id;
                        else
                            $doc_num = $order->order_number.'/'.$order->order_copy_id.'/'.$order->order_postfix;
                        return 'Утратило силу в связи с приказом '.Html::a('№'.$doc_num, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
                    }*/
                    return 'Coming soon';
                }, 'format' => 'raw', 'filter' => [1 => "Актуально", 0 => "Утратило силу"]],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>