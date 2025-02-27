<?php

/* @var $this yii\web\View */
/* @var $manHoursResult array */
/* @var $participantsResult array */

?>

<?php
$this->title = 'Результат отчета по обучающимся';
?>

<p>Человеко-часы: <?= $manHoursResult['result'] ?></p>
<p>Обучающиеся: <?= $participantsResult['result'] ?></p>
