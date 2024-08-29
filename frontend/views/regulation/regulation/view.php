<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\models\work\regulation\RegulationWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model RegulationWork */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::$app->regulationType->get(RegulationTypeDictionary::TYPE_REGULATION), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="regulation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить положение?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date',
            'name',
            ['attribute' => 'order_id', 'label' => 'Приказ', 'value' => function($model){
                /*$order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
                return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));*/
                return 'Coming soon';
            }, 'format' => 'raw'],
            ['attribute' => 'ped_council_number'],
            ['attribute' => 'ped_council_date'],
            ['attribute' => 'par_council_number'],
            ['attribute' => 'par_council_date'],
            ['label' => 'Состояние', 'attribute' => 'state', 'value' => function($model){
                return $model->getStates();
            }, 'format' => 'raw'],
            ['label' => 'Скан положения', 'attribute' => 'scan', 'value' => function ($model) {
                return Html::a($model->scan, Url::to(['regulation/get-file', 'fileName' => $model->scan, 'modelId' => $model->id]));
            }, 'format' => 'raw'],
            'creatorString',
            'editorString',
        ],
    ]) ?>

</div>
