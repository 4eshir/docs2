<?php

/* @var $this yii\web\View */
/* @var $manHoursResult array */
/* @var $participantsResult array */

use backend\forms\report\ManHoursReportForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php
$this->title = 'Результат отчета по обучающимся';
?>

<p>Человеко-часы: <?= $manHoursResult['result'] ?></p>
<p>Обучающиеся: <?= $participantsResult['result'] ?></p>

<?php $form1 = ActiveForm::begin(['method' => 'post', 'action' => ['download-debug-csv', 'type' => ManHoursReportForm::MAN_HOURS_REPORT]]); ?>
    <input type="hidden" name="debugData" value="<?= htmlspecialchars(json_encode($manHoursResult['debugData'], JSON_UNESCAPED_UNICODE)) ?>">
    <?= Html::submitButton('Скачать подробный отчет по человеко-часам', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>

<?php $form2 = ActiveForm::begin(['method' => 'post', 'action' => ['download-debug-csv', 'type' => ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN]]); ?>
    <input type="hidden" name="debugData" value="<?= htmlspecialchars(json_encode($participantsResult['debugData'], JSON_UNESCAPED_UNICODE)) ?>">
    <?= Html::submitButton('Скачать подробный отчет по обучающимся', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
