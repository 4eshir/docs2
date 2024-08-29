<?php

use common\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model RegulationWork */

$this->title = 'Добавить положение';
$this->params['breadcrumbs'][] = ['label' => 'Положения, инструкции, правила', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regulation-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
