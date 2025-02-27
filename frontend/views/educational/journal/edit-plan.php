<?php

use common\helpers\DateFormatter;
use frontend\forms\journal\ThematicPlanForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ThematicPlanForm */

$this->title = 'Редактирование тематического плана'

?>

<div class="plan-edit">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="plan-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'groupId')->hiddenInput()->label(false) ?>

        <table class="table table-bordered">
            <?php foreach ($model->lessonThemes as $index => $lessonTheme): ?>
                <?= $form->field($lessonTheme, "id[$index]")->hiddenInput(['value' => $lessonTheme->id])->label(false) ?>
                <tr>
                    <td>
                        <?=
                            DateFormatter::format(
                                $lessonTheme->trainingGroupLessonWork->lesson_date,
                                DateFormatter::Ymd_dash,
                                DateFormatter::dmY_dot
                            )
                        ?>
                    </td>
                    <td>
                        <?= $lessonTheme->thematicPlanWork->theme ?>
                    </td>
                    <td>
                        <?= $form->field($lessonTheme, "teacher_id[$index]")->widget(Select2::classname(), [
                            'data' => ArrayHelper::map($model->availableTeachers,'id','fullFio'),
                            'size' => Select2::LARGE,
                            'options' => [
                                'value' => $lessonTheme->teacherWork->people_id
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('ФИО педагога');

                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

