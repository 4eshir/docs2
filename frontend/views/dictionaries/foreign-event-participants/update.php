<?php

use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ForeignEventParticipantsWork */

$this->title = 'Редактировать участника деятельности: ' . $model->getFIO(ForeignEventParticipantsWork::FIO_SURNAME_INITIALS);
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFIO(ForeignEventParticipantsWork::FIO_SURNAME_INITIALS), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="foreign-event-participants-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
