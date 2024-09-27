<?php
use yii\helpers\Html;use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\ThematicPlanWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temporary-journal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'theme')->textInput()->label('Тема') ?>

    <?php
    $ct = \app\models\work\ControlTypeWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($ct,'id','name');
    $params = [
        'prompt' => ''
    ];
    echo $form->field($model, 'control_type_id')->dropDownList($items,$params)->label('Форма контроля');

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>