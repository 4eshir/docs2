<?php

use app\models\work\EventsLinkWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\EventWork */

$this->title = 'Добавить мероприятие';
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelEventsLinks' => $modelEventsLinks,
        'modelGroups' => $modelGroups,
    ]) ?>

</div>
