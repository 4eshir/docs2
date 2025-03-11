<?php

/* @var $this yii\web\View */
/* @var $eventResult array */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php
$this->title = 'Результат отчета по мероприятиям';
?>

<table>
    <tr>
        <th>Показатель</th>
        <th>Уровень</th>
        <th>Значение</th>
    </tr>
    <?php foreach($eventResult['result']['levels'] as $index => $result): ?>
        <tr>
            <td>Число учащихся, являющихся участниками</td>
            <td><?= Yii::$app->eventLevel->get($index) ?></td>
            <td><?= $result['participant'] ?></td>
        </tr>
        <tr>
            <td>Число учащихся, являющихся призерами</td>
            <td><?= Yii::$app->eventLevel->get($index) ?></td>
            <td><?= $result['prizes'] ?></td>
        </tr>
        <tr>
            <td>Число учащихся, являющихся победителями</td>
            <td><?= Yii::$app->eventLevel->get($index) ?></td>
            <td><?= $result['winners'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>


<?php $form1 = ActiveForm::begin(['method' => 'post', 'action' => ['download-debug-csv']]); ?>
    <input type="hidden" name="debugData" value="<?= htmlspecialchars(json_encode($eventResult['debugData'], JSON_UNESCAPED_UNICODE)) ?>">
<?= Html::submitButton('Скачать подробный отчет учету достижений в мероприятиях', ['class' => 'btn btn-link']) ?>
<?php ActiveForm::end(); ?>