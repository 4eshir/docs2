<?php

use common\components\wizards\AlertMessageWizard;
use common\helpers\html\HtmlBuilder;
use common\models\scaffold\DocumentOrder;
use frontend\models\work\order\DocumentOrderWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\order\OrderTrainingWork */
/* @var $people */
/* @var $groups */
/* @var $groupParticipant */
/* @var $transferGroups */
/* @var $scanFile */
/* @var $docFiles */
/* @var $groupCheckOption */
/* @var $groupParticipantOption */
/* @var $error */
$this->title = 'Изменить приказ об образовательной деятельности № '. $model->order_number;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>
<div class="order-training-update">
    <?php
        switch ($error) {
            case DocumentOrderWork::ERROR_DATE_PARTICIPANT:
                echo HtmlBuilder::createWarningMessage('Невозможно применить действие к ученикам.', 'Ошибка выбора даты приказа');
                break;
            case DocumentOrderWork::ERROR_RELATION:
                echo HtmlBuilder::createWarningMessage('Невозможно применить действие к ученикам.', 'Выбранные обучающиеся задействованы в других приказах');
                break;
        }
    ?>
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