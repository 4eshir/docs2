<?php

use frontend\models\work\event\EventWork;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model EventWork */
/* @var $people PeopleWork */
/* @var $regulations RegulationWork */
/* @var $branches array */
/* @var $protocolFiles */
/* @var $photoFiles */
/* @var $reportingFiles */
/* @var $otherFiles */

$this->title = 'Редактировать мероприятие: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="event-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'regulations' => $regulations,
        'branches' => $branches,
        'protocolFiles' => $protocolFiles,
        'photoFiles' => $photoFiles,
        'reportingFiles' => $reportingFiles,
        'otherFiles' => $otherFiles,
    ]) ?>

</div>
