<?php

use common\models\work\general\CompanyWork;
use common\models\work\general\PeopleWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $companies CompanyWork */

$this->title = 'Редактировать человека: ' . $model->surname.' '.$model->firstname.' '.$model->patronymic;
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->surname.' '.$model->firstname.' '.$model->patronymic, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="people-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
        'companies' => $companies,
    ]) ?>

</div>
