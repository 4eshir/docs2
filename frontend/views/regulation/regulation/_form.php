<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use common\models\work\regulation\RegulationWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model RegulationWork */
/* @var $form yii\widgets\ActiveForm */
/* @var $scanFile */
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
        'dateFormat' => 'php:d.m.Y',
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

    <?php if (is_array($scanFile) && count($scanFile) > 0): ?>
        <table class="table table-bordered">
            <?php foreach ($scanFile as $file): ?>
                <tr>
                    <td><?= $file['link'] ?></td>
                    <td><?= Html::a('Удалить', Url::to(['delete-file', 'modelId' => $model->id, 'fileId' => $file['id']])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?= $form->field($model, 'regulation_type')->hiddenInput(['value' => RegulationTypeDictionary::TYPE_REGULATION])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

