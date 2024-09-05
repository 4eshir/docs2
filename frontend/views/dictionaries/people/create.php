<?php

use common\models\work\general\CompanyWork;
use common\models\work\general\PeopleWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $companies CompanyWork */

$this->title = 'Добавить человека';
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
        'companies' => $companies,
    ]) ?>

</div>
