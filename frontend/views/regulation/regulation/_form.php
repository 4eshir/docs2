<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\models\work\regulation\RegulationWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model RegulationWork */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="regulation-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]])->label('Дата положения') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

    <?php
    /*$orders = \app\models\work\DocumentOrderWork::find()->where(['!=', 'order_name', 'Резерв'])->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');*/
    $params = [
        'prompt' => '---'
    ];

    echo $form->field($model, "order_id")->dropDownList([1 => 'boobs'],$params)->label('Приказ');

    ?>

    <?= $form->field($model, 'ped_council_number')->textInput() ?>

    <?= $form->field($model, 'ped_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата совета',
            'class'=> 'form-control',
            'autocomplete'=>'off',
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]]) ?>

    <?= $form->field($model, 'par_council_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата собрания',
            'class'=> 'form-control',
            'autocomplete'=>'off',
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]]) ?>



    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан положения')?>

    <?= $form->field($model, 'regulation_type')->hiddenInput(['value' => RegulationTypeDictionary::TYPE_REGULATION])->label(false) ?>

    <?php
/*    if (strlen($model->scan) > 2)
        echo '<h5>Загруженный файл: '.Html::a($model->scan, \yii\helpers\Url::to(['regulation/get-file', 'fileName' => $model->scan])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['regulation/delete-file', 'fileName' => $model->scan, 'modelId' => $model->id, 'type' => 'scan'])).'</h5><br>';
    */?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

