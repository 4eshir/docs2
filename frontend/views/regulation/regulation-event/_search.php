<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchRegulation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="regulation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'ped_council_number') ?>

    <?php // echo $form->field($model, 'ped_council_date') ?>

    <?php // echo $form->field($model, 'par_council_number') ?>

    <?php // echo $form->field($model, 'par_council_date') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'scan') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
