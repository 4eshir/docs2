<?php

use frontend\models\work\educational\TrainingProgramWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model TrainingProgramWork */
/* @var $modelAuthor array */
/* @var $modelThematicPlan array */

$this->title = 'Редактировать образовательную программу: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="training-program-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelAuthor' => $modelAuthor,
        'modelThematicPlan' => $modelThematicPlan,
    ]) ?>

</div>
