<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\DocumentOrder;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\work\order\OrderTrainingWork */
/* @var $people */
/* @var $groups */
/* @var $groupParticipant */
/* @var $transferGroups */
/* @var $scanFile */
/* @var $docFiles */
/* @var $groupCheckOption */
/* @var $groupParticipantOption */
$this->title = 'Изменить приказ об образовательной деятельности № '. $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="order-training-update">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

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
        'groupCheckOption' => $groupCheckOption,
        'groupParticipantOption' => $groupParticipantOption,
    ]) ?>
</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= DocumentOrder::tableName() ?>', 'index.php?r=order/order-training/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>