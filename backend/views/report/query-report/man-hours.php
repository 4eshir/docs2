<?php

use backend\forms\report\ManHoursReportForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ManHoursReportForm */
/* @var $form ActiveForm */
?>

<?php
$this->title = 'Генерация отчета по обучающимся';
?>

<style>
    .block-report{
        background: #e9e9e9;
        width: auto;
        padding: 10px 10px 0 10px;
        margin-bottom: 20px;
        border-radius: 10px;
        margin-right: 10px;
    }
</style>

<div class="man-hours-report-form">

    <h5><b>Введите период для генерации отчета</b></h5>
    <div class="col-xs-6 block-report">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'startDate', ['template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('С') ?>
    </div>

    <div class="col-xs-6 block-report">
        <?= $form->field($model, 'endDate', [ 'template' => '{label}&nbsp;{input}',
            'options' => ['class' => 'form-group form-inline']])->widget(\yii\jui\DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => '',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])->label('По') ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div style="max-width: 100%">
        <div class="col-xs-8 block-report">
            <?= $form->field($model, 'branch')->checkboxList(Yii::$app->branches->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="branch-'. $index .'">
                            <input id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
            }])->label('Отдел');
            ?>
        </div>
        <div class="col-xs-8 block-report">
            <?= $form->field($model, 'focus')->checkboxList(Yii::$app->focus->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="focus-'. $index .'">
                            <input id="focus-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
            }])->label('Направленность');
            ?>
        </div>
        <div class="col-xs-8 block-report">
            <?= $form->field($model, 'allowRemote')->checkboxList(Yii::$app->allowRemote->getList(), ['item' => function ($index, $label, $name, $checked, $value) {
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="allow-'. $index .'">
                            <input id="allow-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
            }])->label('Форма реализации');
            ?>
        </div>
        <div class="col-xs-8 block-report">
            <?php
            $arr = ['1' => 'Бюджет', '0' => 'Внебюджет'];
            echo $form->field($model, 'budget')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                        <label for="budget-'. $index .'">
                            <input id="budget-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
            }])->label('Основа');
            ?>
        </div>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div style="min-width: 100%">
        <div class="col-xs-8 block-report" style="width: 96%">
            <?= $form->field($model, 'type')->checkboxList(
                [
                    ManHoursReportForm::MAN_HOURS_REPORT => 'Кол-ву человеко-часов',
                    ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_IN => 'Кол-ву обучающихся, начавших обучение до начала заданного периода и завершивших обучение в заданный период',
                    ManHoursReportForm::PARTICIPANT_START_IN_FINISH_AFTER => 'Кол-ву обучающихся, начавших обучение в заданный период и завершивших обучение после окончания заданного периода',
                    ManHoursReportForm::PARTICIPANT_START_IN_FINISH_IN => 'Кол-ву обучающихся, начавших обучение после начала заданного периода и завершивших обучение до окончания заданного периода',
                    ManHoursReportForm::PARTICIPANT_START_BEFORE_FINISH_AFTER => 'Кол-ву обучающихся, начавших обучение до начала заданного периода и завершивших обучение после окончания заданного периода'
                ],
                [
                    'item' => function($index, $label, $name, $checked, $value)
                    {
                        return '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black">
                                    <label for="interview-'. $index .'">
                                        <input onchange="showHours()" id="interview-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                        <span></span>
                                        '. $label .'
                                    </label>
                                </div>';
                    }
                ])->label('Сгенерировать отчет по'); ?>
        </div>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="col-xs-8 block-report" id="unic" style="display: none">
        <?php
        $arr = [ManHoursReportForm::PARTICIPANTS_ALL => 'Все обучающиеся', ManHoursReportForm::PARTICIPANTS_UNIQUE => 'Уникальные обучающиеся'];
        echo $form->field($model, 'unic')->radioList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="unic-'. $index .'">
                        <input style="margin-left: -20px" id="unic-'. $index .'" name="'. $name .'" type="radio" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Метод подсчета обучающихся');
        ?>
    </div>
    <div class="col-xs-8 block-report" id="hours" style="display: none">
        <?php
        $arr = [ManHoursReportForm::MAN_HOURS_FAIR => 'Метод, учитывающий неявки', ManHoursReportForm::MAN_HOURS_ALL => 'Метод, игнорирующий неявки'];
        echo $form->field($model, 'method')->radioList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                    <label for="methods-'. $index .'">
                        <input style="margin-left: -20px" id="methods-'. $index .'" name="'. $name .'" type="radio" '. $checked .' value="'. $value .'">
                        '. $label .'
                    </label>
                </div>';
        }])->label('Метод подсчета человеко-часов');
        ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>


    <div class="col-xs-8 block-report" id="teachers" style="display: none">
        <?= $form->field($model, 'teacher')->dropDownList(
                ArrayHelper::map($model->teachers, 'id', 'fullName'),
                ['prompt' => 'Все педагоги']
        )->label('Педагог');
        ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>


    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <div class="form-group">
        <?= Html::submitButton('Генерировать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    function showHours()
    {
        var elem = document.getElementById('interview-0');
        var hour = document.getElementById('hours');
        var teach = document.getElementById('teachers');
        if (elem.checked) { hour.style.display = "block"; teach.style.display = "block"; }
        else { hour.style.display = "none"; teach.style.display = "none"; }

        var elem1 = document.getElementById('interview-1');
        var elem2 = document.getElementById('interview-2');
        var elem3 = document.getElementById('interview-3');
        var unic = document.getElementById('unic');
        if (elem1.checked || elem2.checked || elem3.checked) { unic.style.display = "block"; }
        else { unic.style.display = "none"; }
    }

</script>