<?php

use common\helpers\html\HtmlBuilder;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\educational\TrainingProgramWork */
/* @var $modelAuthor */
/* @var $modelThematicPlan */
/* @var $mainFile */
/* @var $docFiles */
/* @var $contractFile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-program-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, "thematic_direction")->dropDownList(Yii::$app->thematicDirection->getFullnameList(), ['params' => '---']); ?>

    <?= $form->field($model, "level")->dropDownList([1, 2, 3]);?>

    <?= $form->field($model, 'ped_council_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '1980:2100',
        ]]) ?>

    <?= $form->field($model, 'ped_council_number')->textInput(['maxlength' => true]) ?>

    <?php if (strlen($modelAuthor) > 10): ?>
        <?= $modelAuthor; ?>
    <?php endif; ?>

    <?= $form->field($model, 'capacity')->textInput() ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Возраст учащихся</h4></div>
            <div class="panel-body">
                <?= $form->field($model, 'student_left_age')->textInput(['value' => $model->student_left_age == null ? 5 : $model->student_left_age]) ?>
                <?= $form->field($model, 'student_right_age')->textInput(['value' => $model->student_right_age == null ? 18 : $model->student_right_age]) ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, "focus")->dropDownList(Yii::$app->focus->getList()); ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Отдел(-ы) - место реализации</h4></div>
            <div class="checkBlock">
                <?= $form->field($model, 'branches')->checkboxList(Yii::$app->branches->getOnlyEducational(), [
                    'item' => function($index, $label, $name, $checked, $value) {
                        $checked = $checked ? 'checked' : '';
                        return "<div 'class'='col-sm-12'><label><input class='sc' type='checkbox' {$checked} name='{$name}'value='{$value}'> {$label}</label></div>";
                    }])->label(false) ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, "allow_remote")->dropDownList(Yii::$app->allowRemote->getList()); ?>

    <?= $form->field($model, 'hour_capacity')->textInput() ?>

    <?= $form->field($model, 'certificate_type')->dropDownList(Yii::$app->certificateType->getList())->label('Итоговая форма контроля'); ?>

    <div style="border: 1px solid #cccccc; border-radius: 5px; padding: 10px; margin-bottom: 10px">
        <?= $form->field($model, 'is_network')->checkbox(['onchange' => 'CheckNetwork(this)']) ?>

        <div id="contractNetwork" style="display: <?php echo $model->is_network == 0 ? 'none' : 'block' ?>">
            <?= $form->field($model, 'contractFile')->fileInput() ?>

            <?php if (strlen($contractFile) > 10): ?>
                <?= $contractFile; ?>
            <?php endif; ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => '6', 'style' => ['resize' => 'none']])->label('Описание') ?>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'actual')->checkbox(); ?>

    <?= $form->field($model, 'utpFile')->fileInput() ?>

    <?php if (strlen($modelThematicPlan) > 10): ?>
        <?= $modelThematicPlan; ?>
    <?php endif; ?>

    <?= $form->field($model, 'mainFile')->fileInput() ?>

    <?php if (strlen($mainFile) > 10): ?>
        <?= $mainFile; ?>
    <?php endif; ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true]) ?>

    <?php if (strlen($docFiles) > 10): ?>
        <?= $docFiles; ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script type="text/javascript">
    function CheckNetwork(main)
    {
        let elem = document.getElementById("contractNetwork");
        console.log(main.checked);
        if (main.checked) elem.style.display = "block";
        else elem.style.display = "none";
    }
</script>