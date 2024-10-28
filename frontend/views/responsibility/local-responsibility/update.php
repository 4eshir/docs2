<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */

$this->title = 'Редактировать ответственность работника: ' . $model->people->secondname.' '.$model->responsibilityType->name;
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->people->secondname.' '.$model->responsibilityType->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="local-responsibility-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
