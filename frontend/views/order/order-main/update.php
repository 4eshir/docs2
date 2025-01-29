<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\work\order\OrderMainWork */
/* @var $people */
/* @var $users */
/* @var $modelExpire */
/* @var $orders */
/* @var $regulations */
/* @var $modelChangedDocuments */
/* @var $scanFile */
/* @var $docFiles */



$this->title = 'Приказ об основной деятельности №' . $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказ об основной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="order-main-update">
    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'users' => $users,
        'modelExpire' => $modelExpire,
        'orders' => $orders,
        'regulations' => $regulations,
        'modelChangedDocuments' => $modelChangedDocuments,
        'scanFile' => $scanFile,
        'docFiles' => $docFiles,
    ]) ?>
</div>
