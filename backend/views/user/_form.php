<?php

use backend\models\forms\UserForm;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model->entity, 'firstname')->textInput() ?>
    <?= $form->field($model->entity, 'surname')->textInput() ?>
    <?= $form->field($model->entity, 'patronymic')->textInput() ?>
    <?= $form->field($model->entity, 'username')->textInput() ?>

    <?php if (is_null($model->entity->password_hash)): ?>
        <?= $form->field($model->entity, 'password_hash')->textInput(); ?>
    <?php endif; ?>

    <?= $form->field($model->entity, 'aka')->widget(Select2::classname(), [
        'data' => ArrayHelper::map($model->peoples, 'id', 'fullFio'),
        'size' => Select2::LARGE,
        'options' => ['prompt' => '---'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('Также является'); ?>

    <?= $form->field($model, 'userPermissions')->checkboxList(
        ArrayHelper::map($model->permissions, 'id', 'name'),
        [
            'class' => 'base',
            'item' => function ($index, $label, $name, $checked, $value) {
                if ($checked == 1) {
                    $checked = 'checked';
                }
                return
                    '<div class="checkbox" class="form-control">
                            <label style="margin-bottom: 0px" for="branch-' . $index .'">
                                <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                '. $label .'
                            </label>
                        </div>';
            }
        ]
    )->label('<u>Правила доступа</u>')
    ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
