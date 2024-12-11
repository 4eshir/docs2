<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model \app\models\work\order\OrderEventWork */
/* @var $people */
/* @var $modelActs */
/* @var $nominations */
/* @var $teams */
$this->title = 'Добавить приказ об участии';
$this->params['breadcrumbs'][] = ['label' => 'Приказы об участии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'modelActs' => $modelActs,
        'nominations' => $nominations,
        'teams' => $teams,
    ]) ?>

</div>


