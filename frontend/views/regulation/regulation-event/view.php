<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\helpers\files\FilesHelper;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model RegulationWork */

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::$app->regulationType->get(RegulationTypeDictionary::TYPE_EVENT), 'url' => ['index']];
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
            ['attribute' => 'order_id', 'label' => 'Приказ', 'value' => function(RegulationWork $model){
                /*$order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_id])->one();
                return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));*/
                return 'Coming soon';
            }, 'format' => 'raw'],
            ['label' => 'Скан положения', 'attribute' => 'scan', 'value' => function (RegulationWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_SCAN), 'link'));
            }, 'format' => 'raw'],
            'creatorString',
            'editorString',
        ],
    ]) ?>

</div>
