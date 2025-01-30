<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\work\order\OrderTrainingWork */
/* @var $people */
/* @var $groups */
/* @var $groupParticipant */
/* @var $transferGroups */
/* @var $scanFile */
/* @var $docFiles */
$this->title = 'Изменить приказ об образовательной деятельности № '. $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-training-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'groups' => $groups,
        'groupParticipant' => $groupParticipant,
        'transferGroups' => $transferGroups,
        'scanFile' => $scanFile,
        'docFiles' => $docFiles,
    ]) ?>
</div>


