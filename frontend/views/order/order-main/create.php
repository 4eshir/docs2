<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentInWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */

$this->title = 'Добавить приказ об основной деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Приказы об осн. деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="">

        <h3><?= Html::encode($this->title) ?></h3>
        <br>

        <?= $this->render('_form', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
        ]) ?>

    </div>

