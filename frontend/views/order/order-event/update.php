<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\work\order\OrderEventWork */
/* @var $people */

$this->title = 'Приказ об участии' . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказ об участии', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="order-main-update">
    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?= $this->render('_form', [
            'model' => $model,
            'people' => $people,
        ]
    ) ?>
</div>
