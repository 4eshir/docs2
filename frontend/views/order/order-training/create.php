<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\work\order\OrderTrainingWork */
/* @var $model */
/* @var $people */
/* @var $groups */
/* @var $groupParticipant */
$this->title = 'Добавить приказ об образовательной деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Приказы об участии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-training-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>
    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people,
        'groups' => $groups,
        'groupParticipant' => $groupParticipant,
    ]) ?>

</div>


