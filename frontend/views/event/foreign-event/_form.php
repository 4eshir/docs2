<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventWork */
/* @var $form yii\widgets\ActiveForm */
?>

<style type="text/css">
    .button {
        position: fixed;
        bottom: 0px;
        background-color: #f5f8f9;
        width: 77%;
        padding-left: 1%;
        padding-top: 1%;
        padding-right: 1%;
        padding-bottom: 1%; /*104.5px is half of the button width*/
    }
    .test{
        height:1000px;

    }
    .row {
        margin: 0px;
    }

    .toggle-wrapper {

        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        column-gap: .25em;
    }

    .toggle-checkbox:not(:checked) + .off,
    .toggle-checkbox:checked ~ .on {
        font-weight: 700;
    }

    .toggle-checkbox {
        -webkit-appearance: none;
        appearance: none;
        position: absolute;
        z-index: 1;
        border-radius: 3.125em;
        width: 4.05em;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        margin-left: -2em!important;
    }

    .toggle-container {
        position: relative;
        border-radius: 3.125em;
        width: 4.05em;
        height: 1.5em;
        background-color: #ccc;
        background-size: .125em .125em;
    }

    .toggle-button {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: .0625em;
        left: .0625em;
        border-radius: inherit;
        width: 2.55em;
        height: calc(100% - .125em);
        background-color: #FFA23A;
        box-shadow: 0 .125em .25em rgb(0 0 0 / .6);
        transition: left .4s;

    .toggle-checkbox:checked ~ .toggle-container > & {
        left: 1.4375em;
    }

    &::before {
         content: '';
         position: absolute;
         top: inherit;
         border-radius: inherit;
         width: calc(100% - .375em);
         height: inherit;
         /*background-image: linear-gradient(to right, #0f73a8, #57cfe2, #b3f0ff);*/
     }

    &::after {
         content: '';
         position: absolute;
         width: .5em;
         height: 38%;
         /*background-image: repeating-linear-gradient(to right, #d2f2f6 0 .0625em, #4ea0ae .0625em .125em, transparent .125em .1875em);*/
     }
    }
</style>


<script type="text/javascript">
    window.onload = function(){
        let elem = document.getElementsByClassName("date_achieve");
        let orig = document.getElementById('foreigneventwork-finish_date');
        elem[elem.length - 1].value = orig.value;
    }
</script>


<div class="foreign-event-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['readonly' => true]) ?>

    <?php
    $company = \app\models\work\CompanyWork::find()->where(['id' => $model->company_id])->one();
    echo $form->field($model, 'company')->textInput(['readonly' => true, 'value' => $company->name])->label('Организатор');
    ?>

    <?= $form->field($model, 'start_date')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'finish_date')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['readonly' => true]) ?>

    <?php
    $ways = \app\models\work\EventWayWork::find()->where(['id' => $model->event_way_id])->one();
    echo $form->field($model, 'event_way')->textInput(['readonly' => true, 'value' => $ways->name])->label('Формат проведения');
    ?>

    <?php
    $levels = \app\models\work\EventLevelWork::find()->where(['id' => $model->event_level_id])->one();
    echo $form->field($model, 'event_level')->textInput(['readonly' => true, 'value' => $levels->name])->label('Уровень');
    ?>

    <?php
        $icon = '❌';
        if ($model->is_minpros)
            $icon = '✅';
        echo '<div class="form-group field-foreigneventwork-is_minpros has-success"><label>'.$icon.' Входит в перечень Минпросвещения РФ</label><div class="help-block"></div></div>';
    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Акты участия</h4></div>
            <?php
            $parts = \app\models\work\TeacherParticipantWork::find()->where(['foreign_event_id' => $model->id])->all();
            $editIcon = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>';
            $deleleIcon = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>';
            if ($parts != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Участник</b></h4></td>
                            <td style="border-bottom: 2px solid black; "><h4><b>Отдел(-ы)</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Педагог</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Направленность</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Номинация</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Команда</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Форма реализации</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black;"><h4><b>Материалы</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black; width: 50px;"><h4><b></b></h4></td>

                       </tr>';
                foreach ($parts as $partOne) {
                    $partOnePeople = \app\models\work\ForeignEventParticipantsWork::find()->where(['id' => $partOne->participant_id])->one();
                    $partFiles = \app\models\work\ParticipantFilesWork::find()->where(['teacher_participant_id' => $partOne->id])->one();
                    $partOneTeacher = \app\models\work\PeopleWork::find()->where(['id' => $partOne->teacher_id])->one();
                    $partTwoTeacher = \app\models\work\PeopleWork::find()->where(['id' => $partOne->teacher2_id])->one();
                    $teachersStr = '';
                    if ($partOneTeacher !== null) $teachersStr .= $partOneTeacher->shortName;
                    if ($partTwoTeacher !== null) $teachersStr .= '<br>'.$partTwoTeacher->shortName;
                    $team = \app\models\work\TeamWork::find()->where(['teacher_participant_id' => $partOne->id])->one();
                    $realizes = \app\models\work\AllowRemoteWork::find()->where(['id' => $partOne->allow_remote_id])->one();

                    echo '<tr style="font-size: 1.2em;"><td style="padding-left: 20px">'. $partOnePeople->shortName.'&nbsp;</label>'.'</td>'.

                        '<td style="padding-left: 20px">'. $partOne->getBranchsString().'&nbsp;</label>'.'</td>'.
                        '<td style="padding-left: 20px">'.$teachersStr.'</td>'.
                        '<td style="padding-left: 10px">'.$partOne->focus0->name.'</td>'.
                        '<td style="padding-left: 10px">'. $partOne->nomination .'</td>'.
                        '<td style="padding-left: 10px">'.$team->teamNameWork->name.'</td>'.
                        '<td style="padding-left: 10px">'.$realizes->name.'</td>';
                    if ($partFiles == null)
                        echo '<td style="padding-left: 10px; text-align: center;"> -- </td>';
                    else
                        echo '<td style="padding-left: 10px; text-align: center; max-width: 150px; overflow-wrap: break-word;">'.Html::a($partFiles->filename, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $partFiles->filename, 'type' => 'participants'])).'</td>';
                    echo '<td style="padding-left: 10px">'.
                        Html::a($editIcon, \yii\helpers\Url::to(['foreign-event/update-participant', 'id' => $partOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-primary', 'style' => 'margin: 2px;']). ' ' .
                        /*Html::a($deleleIcon, \yii\helpers\Url::to(['foreign-event/delete-participant', 'id' => $partOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger', 'style' => 'width: 40px; margin: 2px;']).*/

                        '</td></tr>';
                }
                echo '</table>';
            }
            ?>
        </div>
    </div>

    <?= $form->field($model, 'min_participants_age')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'max_participants_age')->textInput(['readonly' => true]) ?>


    <div class="row">
        <div class="panel panel-default">
            <table style="width: 100%; border-bottom: 1px solid #dddddd">
                <tr style="background: #f5f5f5;">
                    <td style="width: 95%">
                        <div class="panel-heading"><h4><i class="glyphicon glyphicon-sunglasses"></i>Победители и призеры</h4></div>
                    </td>
                    <td style="width: 5%">
                        <div>
                            <div data-html="true" style="width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Достижением считается любое призовое место или спец. номинация. Участие не является достижением&#10&#10Дата наградного документа изменяется только в случае, если она не совпадает с датой на реальном документе&#10&#10Номер наградного документа заполняется только при его наличии на реальном документе">❔</div>
                        </div>
                    </td>
                </tr>
            </table>
            
            
            <?php
            $parts = \app\models\work\ParticipantAchievementWork::find()->joinWith('teacherParticipant teacherParticipant')->where(['teacherParticipant.foreign_event_id' => $model->id])->all();
            if ($parts != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr>
                        <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Участник</b></h4></td>
                        <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Статус</b></h4></td>
                        <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Достижение</b></h4></td>
                        <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Акт участия</b></h4></td>
                        <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Номер сертификата</b></h4></td>
                        <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Дата сертификата</b></h4></td>
                        <td style="padding-left: 20px; border-bottom: 2px solid black; width: 110px;"></td>
                      </tr>';
                foreach ($parts as $partOne) {
                    if ($partOne->teacherParticipantWork->team == null)
                        $namePart = $partOne->teacherParticipantWork->participantWork->shortName;
                    else
                    {
                        $teamParts = \app\models\work\TeamWork::find()->where(['team_name_id' => $partOne->team_name_id])->all();
                        $namePart = 'Команда: ';
                        foreach ($teamParts as $onePart)
                        {
                            $namePart .= $onePart->teacherParticipantWork->participantWork->shortName . ', ';
                        }
                        $namePart = mb_substr($namePart, 0, -2);
                    }
                    echo '<tr style="font-size: 1.2em;">
                            <td style="padding-left: 20px">'.$namePart.'</td>
                            <td style="padding-left: 20px">'.$partOne->statusString.'</td>
                            <td style="padding-left: 20px">'.$partOne->achievment.'</td>
                            <td style="padding-left: 20px">'.$partOne->actParticipationString.'</td>
                            <td style="padding-left: 20px">'.$partOne->cert_number.'</td>
                            <td style="padding-left: 20px">'.$partOne->date.'</td>'
                        .'<td>&nbsp;'.Html::a($editIcon, \yii\helpers\Url::to(['foreign-event/update-achievement', 'id' => $partOne->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary'])
                        .Html::a($deleleIcon, \yii\helpers\Url::to(['foreign-event/delete-achievement', 'id' => $partOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger', 'style' => 'width: 40px; margin-left: 5px;']).'</td>
                         </tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items1', // required: css class selector
                    'widgetItem' => '.item1', // required: css class
                    'limit' => 50, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item1', // css class
                    'deleteButton' => '.remove-item1', // css class
                    'model' => $modelAchievement[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'fio',
                    ],
                ]); ?>

                <div class="container-items1" style="padding: 0; margin: 0"><!-- widgetContainer -->
                    <?php foreach ($modelAchievement as $i => $modelAchievementOne): ?>
                    <div class="item1 panel panel-default" style="padding: 0; margin: 0"><!-- widgetBody -->
                        <div class="panel-heading" style="padding: 0; margin: 0">
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item1 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item1 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-6">
                                <?php
                                $partsAch = \app\models\work\ParticipantAchievementWork::find()->joinWith('teacherParticipant teacherParticipant')->where(['teacherParticipant.foreign_event_id' => $model->id])->all();
                                $partsArr = [];
                                foreach ($partsAch as $partAch)
                                    $partsArr[] = $partAch->teacher_participant_id;

                                $partsTeam = \app\models\work\TeamWork::find()->joinWith('teacherParticipant teacherParticipant')->where(['teacherParticipant.foreign_event_id' => $model->id])->andWhere(['IS NOT','team_name_id', null])->all();
                                foreach ($partsTeam as $partTeam)
                                    $partsArr[] = $partTeam->teacher_participant_id;

                                $partsTeam = \app\models\work\TeacherParticipantWork::find()->joinWith(['teams teams'])->where(['teacher_participant.foreign_event_id' => $model->id])->andWhere(['IS NOT','teams.team_name_id', null])
                                    ->groupBy(['focus'])->groupBy(['nomination'])->groupBy(['teams.team_name_id']);

                                $parts = \app\models\work\TeacherParticipantWork::find()->where(['foreign_event_id' => $model->id])->andWhere(['NOT IN', 'id', $partsArr])->union($partsTeam)->all();

                                $items = \yii\helpers\ArrayHelper::map($parts,'id','actString');
                                $params = [
                                    'prompt' => '--'
                                ];
                                echo $form->field($modelAchievementOne, "[{$i}]fio")->dropDownList($items,$params)->label('Акт участия');
                                ?>
                            </div>
                            <div class="col-xs-4">
                                <?php

                                echo $form->field($modelAchievementOne, "[{$i}]achieve")->textInput();

                                ?>
                            </div>
                            <div class="col-xs-4">
                                <?php

                                echo $form->field($modelAchievementOne, "[{$i}]cert_number")->textInput();

                                ?>
                            </div>

                            <div class="col-xs-4">

                                <?= $form->field($modelAchievementOne, "[{$i}]date")->widget(DatePicker::class, [
                                    'dateFormat' => 'php:Y-m-d',
                                    'language' => 'ru',
                                    'options' => [
                                        'placeholder' => 'Дата',
                                        'class'=> 'form-control date_achieve',
                                        'autocomplete'=>'off'

                                    ],
                                    'clientOptions' => [
                                        'changeMonth' => true,
                                        'changeYear' => true,
                                        'yearRange' => '2000:2050',
                                        //'showOn' => 'button',
                                        //'buttonText' => 'Выбрать дату',
                                        //'buttonImageOnly' => true,
                                        //'buttonImage' => 'images/calendar.gif'
                                    ]]) ?>
                            </div>
                            <div class="col-xs-4" style="margin-top: 30px;">
                                <?php

                                echo '<div class="toggle-wrapper form-group field-participantsachievementextended-'.$i.'-winner">
                                            <input type="hidden" value="0" id="participantsachievementextended-'.$i.'-winner" name="ParticipantsAchievementExtended['.$i.'][winner]">
                                            <input type="checkbox" value="1" id="participantsachievementextended-'.$i.'-winner" class="toggle-checkbox" name="ParticipantsAchievementExtended['.$i.'][winner]">
                                            <span class="toggle-icon off">Призер</span>
                                            <div class="toggle-container">
                                                <div class="toggle-button"></div>
                                            </div>
                                            <span class="toggle-icon on">Победитель</span>
                                            <div class="help-block"></div>
                                       </div>';

                                ?>
                            </div>
                            <div class="panel-body" style="padding: 0; margin: 0"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'business_trip')->checkbox(['id' => 'tripCheckbox', 'onchange' => 'checkTrip()']) ?>

    <div id="divEscort" <?php echo $model->business_trip == 0 ? 'hidden' : '' ?>>
        <?php
        $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
        $params = [
            'prompt' => 'не выбрано',
        ];
        echo $form->field($model, 'escort_id')->dropDownList($items,$params);

        ?>
    </div>

    <div id="divOrderTrip" <?php echo $model->business_trip == 0 ? 'hidden' : '' ?>>
        <?php
        $orders = \app\models\work\DocumentOrderWork::find()->where(['type' => 1])->andWhere(['>=', 'order_date', date('Y-m-d', strtotime($model->start_date . '-6 month'))])->all();

        $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
        $params = [
            'prompt' => '--',
        ];
        echo $form->field($model, 'order_business_trip_id')->dropDownList($items,$params);

        ?>
    </div>

    <?php
    $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $model->order_participation_id])->one();
    if ($order)
        echo $form->field($model, 'order_participation')->textInput(['readonly' => true, 'value' => $order->fullName])->label('Приказ об участии');
    ?>

    <?php
    $orders = \app\models\work\DocumentOrderWork::find()->where(['in', 'type', [1, 2]])->andWhere(['>=', 'order_date', date('Y-m-d', strtotime($model->start_date . '-9 month'))])->all();

    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [
        'prompt' => '--',
    ];
    echo $form->field($model, 'add_order_participation_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'docsAchievement')->fileInput() ?>

    <?php
    if (strlen($model->docs_achievement) > 2)
        echo '<h5>Загруженный файл: '.Html::a($model->docs_achievement, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $model->docs_achievement, 'type' => 'achievements_files'])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['foreign-event/delete-file', 'fileName' => $model->docs_achievement, 'modelId' => $model->id, 'type' => 'docs'])).'</h5><br>';
    ?>
    <div class="form-group">
        <div class="button">

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Сохранить изменения? Если были загружены новые файлы заявок/достижений, то они заменят более старые',
                    'method' => 'post',
                ],]) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>


<script>

    var counter = 1;

    function ClickBranch($this, $index)
    {
        if ($index == 4)
        {
            let parent = $this.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
            let childs = parent.querySelectorAll('.col-xs-4');
            let first_gen = childs[1].querySelectorAll('.form-group');
            let second_gen = first_gen[3].querySelectorAll('.form-control');
            if (second_gen[0].hasAttribute('disabled'))
                second_gen[0].removeAttribute('disabled');
            else
            {
                second_gen[0].value = 1;
                second_gen[0].setAttribute('disabled', 'disabled');
            }
        }
        
    }

    function checkTrip()
    {
        var chkBox = document.getElementById('tripCheckbox');
        if (chkBox.checked)
        {
            $("#divEscort").removeAttr("hidden");
            $("#divOrderTrip").removeAttr("hidden");
        }
        else
        {
            $("#divEscort").attr("hidden", "true");
            $("#divOrderTrip").attr("hidden", "true");
        }
    }
</script>

<?php
$js =<<< JS
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {

        let elems = document.getElementsByClassName('base');
        
        let values = [];
        for (let i = 0; i < elems[0].children.length; i++)
            if (elems[1].children[i].childElementCount > 0)
                values[i] = elems[0].children[i].children[0].children[0].value;
        for (let j = 1; j < elems.length; j++)
            for (let i = 0; i < elems[1].children.length; i++)
                if (elems[j].children[i].childElementCount > 0)
                   elems[j].children[i].children[0].children[0].value = values[i]; 


    });

JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);

$js =<<< JS
    $(".dynamicform_wrapper1").on("afterInsert", function(e, item) {
        let elem = document.getElementsByClassName("date_achieve");
        let orig = document.getElementById('foreigneventwork-finish_date');
        elem[elem.length - 1].value = orig.value;
    });

JS;

$this->registerJs($js, \yii\web\View::POS_LOAD);

/*let elem = document.getElementById('foreigneventparticipantsextended-0-allow_remote_id');
        if (elem.hasAttribute('disabled') && $(this).is(':checked') == true)
            elem.removeAttribute('disabled');
        if (!elem.hasAttribute('disabled') && $(this).is(':checked') == false)
        {
            elem.value = 1;
            elem.setAttribute('disabled', 'disabled');
        }*/
?>
