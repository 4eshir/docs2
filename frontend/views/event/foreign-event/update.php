<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventWork */

$this->title = 'Редактировать: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="foreign-event-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelParticipants' => $modelParticipants,
        'modelAchievement' => $modelAchievement,
    ]) ?>

</div>
