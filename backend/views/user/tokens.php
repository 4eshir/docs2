<?php

use backend\forms\TokensForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TokensForm */

?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <div class="content-container" style="float: left">
        <h3>Выдать токен доступа</h3>
        <br>

        <?php $form = ActiveForm::begin(); ?>

        <?=
        $form->field($model, 'userId')->dropDownList(
            ArrayHelper::map($model->users, 'id', 'fullName')
        )->label('Пользователь');
        ?>

        <?=
        $form->field($model, 'permissionId')->dropDownList(
            ArrayHelper::map($model->permissions, 'id', 'name')
        )->label('Разрешение');
        ?>

        <?=
        $form->field($model, 'branch')->dropDownList(
            Yii::$app->branches->getList(), ['prompt' => '---']
        )->label('Отдел (при необходимости')
        ?>

        <div class="col-xs-12" style="padding-left: 0">
            <div class="col-xs-3" style="padding-left: 0">
                <h4>Время жизни токена</h4>
            </div>

            <div class="col-xs-3">
                <?= $form->field($model, 'duration')->textInput(['type' => 'number', 'style' => 'max-width: 100px', 'value' => 0])->label('Часы') ?>
            </div>

        </div>
        <div class="panel-body" style="padding: 0; margin: 0"></div>

        <div class="form-group">
            <?= Html::submitButton('Выдать токен', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <br>
    <!--<h3>Активные токены</h3>
    <?php
/*
    $levels = \app\models\work\AccessLevelWork::find()->orderBy(['start_time' => SORT_DESC])->all();

    */?>
    <table class="table table-striped">
        <?php
/*
        foreach ($levels as $level)
        {
            echo '<tr>';
            echo '<td>'.$level->userWork->fullName.'</td><td>'.$level->roleFunctionWork->name.'</td><td>'.date('d.m.Y (H:i)', strtotime($level->start_time)).'</td><td>'.date('d.m.Y (H:i)', strtotime($level->end_time)).'</td>'.
                '<td>'.Html::a('Отозвать токен', \yii\helpers\Url::to(['lk/delete-token', 'id' => $level->id]), ['class' => 'btn btn-danger']).'</td>';
            echo '</tr>';
        }

        */?>
    </table>-->

</div>
<div style="width:100%; height:1px; clear:both;"></div>