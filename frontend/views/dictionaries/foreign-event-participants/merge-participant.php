<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\jui\AutoComplete;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventParticipantsWork */

$this->title = 'Слияние участников деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Слияние', 'url' => ['merge-participant']];
?>
<style>
    .block-report{
        background: #e9e9e9;
        width: 45%;
        padding: 10px 10px 0 10px;
        margin-bottom: 20px;
        border-radius: 10px;
        margin-right: 10px;
    }
    .badge {
        padding: 3px 9px 4px;
        font-size: 13px;
        font-weight: bold;
        white-space: nowrap;
        color: #ffffff;
        background-color: #999999;
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }
    .badge:hover {
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }
    .badge-error {
        background-color: #b94a48;
    }
    .badge-error:hover {
        background-color: #953b39;
    }
    .badge-success {
        background-color: #468847;
    }
    .badge-success:hover {
        background-color: #356635;
    }
</style>

<div class="man-hours-report-form">

    <h5><b>Выберите двух участников деятельности</b></h5>
    <div class="col-xs-6 block-report">

        <?php $form = ActiveForm::begin(); ?>

        <?php

        $people = \app\models\work\ForeignEventParticipantsWork::find()->select(['CONCAT(secondname, \' \', firstname, \' \', patronymic, \' \', birthdate, \' (id: \', id, \')\') as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic, ' ', birthdate, ' (id: ', id, ')') as label", 'id as id'])->asArray()->all();

        echo $form->field($model, 'fio1')->widget(
            AutoComplete::className(), [
            'clientOptions' => [
                'source' => $people,

                'select' => new JsExpression("function( event, ui ) {
                    $('#participant_id1').val(ui.item.id); //#memberssearch-family_name_id is the id of hiddenInput.
                    CheckFieldsFill();
                 }"),
            ],
            'options'=>[
                'class'=>'form-control on',
            ]
        ])->label('ФИО участника деятельности №1');

        echo $form->field($model, 'id1')->hiddenInput(['class' => 'part', 'id' => 'participant_id1', 'name' => 'participant1'])->label(false);

        ?>


        <!--<input class="part" type="hidden" id="participant_id1" name="participant1">-->
    </div>

    <div class="col-xs-6 block-report">
        <?php

        $people = \app\models\work\ForeignEventParticipantsWork::find()->select(['CONCAT(secondname, \' \', firstname, \' \', patronymic, \' \', birthdate, \' (id: \', id, \')\') as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic, ' ', birthdate, ' (id: ', id, ')') as label", 'id as id'])->asArray()->all();

        echo $form->field($model, 'fio2')->widget(
            AutoComplete::className(), [
            'clientOptions' => [
                'source' => $people,
                'select' => new JsExpression("function( event, ui ) {
                    let e1 = document.getElementById('participant_id1');
                    let e2 = document.getElementById('participant_id2');

                    $('#participant_id2').val(ui.item.id);
                    $.get(
                            \"" . Url::toRoute('info') . "\", 
                            {id1: e1.value, id2: e2.value},
                        function(res){
                            let elem = document.getElementById('commonBlock');
                            elem.innerHTML = res;
                        }
                    );
                    CheckFieldsFill();
                 }"),
                
            ],
            'options'=>[
                'class'=>'form-control on',
            ]
        ])->label('ФИО участника деятельности №2');

        echo $form->field($model, 'id2')->hiddenInput(['class' => 'part', 'id' => 'participant_id2', 'name' => 'participant2'])->label(false);

        ?>

    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div id="commonBlock" style="display: none">
    </div>
    
    <div id="editBlock" style="display: none; width: 91%;">
        <?= $form->field($model->edit_model, 'secondname')->textInput() ?>

        <?= $form->field($model->edit_model, 'firstname')->textInput() ?>

        <?= $form->field($model->edit_model, 'patronymic')->textInput() ?>

        <?= $form->field($model->edit_model, 'birthdate')->widget(DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            //'dateFormat' => 'dd.MM.yyyy,
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '1980:2050',
                //'showOn' => 'button',
                //'buttonText' => 'Выбрать дату',
                //'buttonImageOnly' => true,
                //'buttonImage' => 'images/calendar.gif'
            ]]) ?>
        <div>
            <?= $form->field($model->edit_model, 'sex')->radioList(array('Мужской' => 'Мужской',
                'Женский' => 'Женский', 'Другое' => 'Другое'), ['value' => $model->sex, 'class' => 'i-checks',
                    'item' => function($index, $label, $name, $checked, $value) {
                        if ($checked == true)
                            $checkedStr = 'checked=""';
                        else
                            $checkedStr = '';
                        $return = '<label class="modal-radio">';
                        $return .= '<input id="rl'.$index.'" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3" style="margin-right: 5px" '.$checkedStr.'>';
                        $return .= '<i></i>';
                        $return .= '<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';

                        return $return;
                    }
                ]) ?>
        </div>


        <?php
        $data = \app\models\work\PersonalDataWork::find()->all();
        $arr = \yii\helpers\ArrayHelper::map($data, 'id', 'name');
        if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 22) )
            echo $form->field($model->edit_model, 'pd')->checkboxList($arr, ['item' => function ($index, $label, $name, $checked, $value) {
                if ($checked == 1) $checked = 'checked';
                return
                    '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                            <label for="branch-'. $index .'">
                                <input class="eb1" id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                '. $label .'
                            </label>
                        </div>';
            }])->label('Запретить разглашение персональных данных:');
        ?>

        <?= $form->field($model, 'id1')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'id2')->hiddenInput()->label(false); ?>
    </div>

    <div class="panel-body" style="padding: 0; margin: 0"></div>

    <div class="form-group">
        <?= Html::submitButton('Объединить участников деятельности', ['id' => 'sub', 'class' => 'btn btn-primary', 'style' =>'display: none',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    function CheckFieldsFill()
    {
        let elem1 = document.getElementById('participant_id1');
        let elem2 = document.getElementById('participant_id2');

        if (elem1.value == elem2.value)
        {
            alert('Выбраны одинаковые участники! Обновите страницу и выберите разных участников');
            return;
        }

        if (elem1.value && elem2.value)
        {
            let main = document.getElementById('commonBlock');
            main.style.display = 'block';
            //main = document.getElementById('fill1');
            //main.style.display = 'block';
            main = document.getElementById('sub');
            main.removeAttribute('disabled');
            main = document.getElementById('mergeparticipantmodel-fio1');
            main.setAttribute('readonly', 'true');
            main = document.getElementById('mergeparticipantmodel-fio2');
            main.setAttribute('readonly', 'true');

            
        }
    }

    function FillEditForm()
    {
        let main = document.getElementById('editBlock');
        main.style.display = 'block';
        main = document.getElementById('fill1');
        main.style.display = 'none';
        main = document.getElementById('sub');
        main.style.display = 'block';
        //заполняем поля редактируемой формы
        main = document.getElementById('foreigneventparticipantswork-secondname');
        let temp = document.getElementById('td-secondname-1');
        main.value = temp.innerHTML;

        main = document.getElementById('foreigneventparticipantswork-firstname');
        temp = document.getElementById('td-firstname-1');
        main.value = temp.innerHTML;

        main = document.getElementById('foreigneventparticipantswork-patronymic');
        temp = document.getElementById('td-patronymic-1');
        main.value = temp.innerHTML;

        main = document.getElementById('foreigneventparticipantswork-birthdate');
        temp = document.getElementById('td-birthdate-1');
        main.value = temp.innerHTML;

        let main1 = document.getElementById('rl0');
        let main2 = document.getElementById('rl1');
        let main3 = document.getElementById('rl2');
        temp = document.getElementById('td-sex-1');
        if (temp.innerHTML == 'Мужской') main1.setAttribute('checked', true);
        if (temp.innerHTML == 'Женский') main2.setAttribute('checked', true);
        if (temp.innerHTML == 'Другое') main3.setAttribute('checked', true);

        main = document.getElementsByClassName('b1');
        temp = document.getElementsByClassName('eb1');
        console.log(main);
        for (let i = 0; i < main.length; i++)
        {
            if (main[i].innerHTML == 'Запрещено') temp[i].setAttribute('checked', true);
        }
        
        main = document.getElementById('mergeparticipantmodel-id1');
        temp = document.getElementById('participant_id1');
        main.value = temp.value;

        main = document.getElementById('mergeparticipantmodel-id2');
        temp = document.getElementById('participant_id2');
        main.value = temp.value;

        //----------------------------------
    }

</script>