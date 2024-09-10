<?php


use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */
/* @var $filesAnswer */
$this->title = 'Добавить исходящий документ';
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-out-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>

    <?= $this->render('_form', [
        'model' => $model,
        'correspondentList' => $correspondentList,
        'availablePositions' => $availablePositions,
        'availableCompanies' => $availableCompanies,
        'mainCompanyWorkers' => $mainCompanyWorkers,
        'filesAnswer' => $filesAnswer,
    ]) ?>

</div>
