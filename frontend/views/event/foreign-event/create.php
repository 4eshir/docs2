<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventWork */

$this->title = 'Добавление';
$this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelParticipants' => $modelParticipants,
        'modelAchievement' => $modelAchievement,
    ]) ?>

</div>
