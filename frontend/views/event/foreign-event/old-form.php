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

    <?= $form->field($model, 'name')->textInput($model->copy == 1 ? ['readonly' => true, 'maxlength' => true, 'disabled' => 'disabled'] : ['readonly' => true, 'maxlength' => true]) ?>

    <?php
    $company = \app\models\work\CompanyWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [
        'prompt' => '--',
    ];
    echo $form->field($model, 'company_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::class,
        $model->copy == 1 ? (
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата начала мероприятия',
                'class'=> 'form-control',
                'autocomplete'=>'off',
                'disabled' =>'disabled'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]]) : ([
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата начала мероприятия',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]
        ])) ?>

    <?= $form->field($model, 'finish_date')->widget(\yii\jui\DatePicker::class,
        $model->copy == 1 ? (
        [
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания мероприятия',
                'class'=> 'form-control',
                'autocomplete'=>'off',
                'disabled' => 'disabled'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]]) : ([
            'dateFormat' => 'php:Y-m-d',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата окончания мероприятия',
                'class'=> 'form-control',
                'autocomplete'=>'off',
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2050',
            ]])) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?php
    $ways = \app\models\work\EventWayWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($ways,'id','name');
    $params = [
    ];
    echo $form->field($model, 'event_way_id')->dropDownList($items,$params);

    ?>

    <?php
    $levels = \app\models\work\EventLevelWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($levels,'id','name');
    $params = [
        'disabled' => 'disabled'
    ];
    $params0 = [
    ];
    echo $form->field($model, 'event_level_id')->dropDownList($items,$model->copy == 1 ? $params : $params0);

    ?>

    <?= $form->field($model, 'is_minpros')->checkbox(); ?>

    <?php $c = 0; ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Участники</h4></div>
            <?php
            $parts = \app\models\work\TeacherParticipantWork::find()->where(['foreign_event_id' => $model->id])->all();
            if ($parts != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Участник</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Педагог</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Направленность</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Команда</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Представленные материалы</b></h4></td></tr>';
                foreach ($parts as $partOne) {
                    $partOnePeople = \app\models\work\ForeignEventParticipantsWork::find()->where(['id' => $partOne->participant_id])->one();
                    $partFiles = \app\models\work\ParticipantFilesWork::find()->where(['participant_id' => $partOnePeople->id])->andWhere(['foreign_event_id' => $partOne->foreign_event_id])->one();
                    $partOneTeacher = \app\models\work\PeopleWork::find()->where(['id' => $partOne->teacher_id])->one();
                    $partTwoTeacher = \app\models\work\PeopleWork::find()->where(['id' => $partOne->teacher2_id])->one();
                    $teachersStr = '';
                    if ($partOneTeacher !== null) $teachersStr .= $partOneTeacher->shortName;
                    if ($partTwoTeacher !== null) $teachersStr .= '<br>'.$partTwoTeacher->shortName;
                    $team = \app\models\work\TeamWork::find()->where(['foreign_event_id' => $model->id])->andWhere(['participant_id' => $partOnePeople->id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.
                        $partOnePeople->shortName.'&nbsp;</label>'.'</h4></td><td style="padding-left: 20px"><h4>'.$teachersStr.'</h4></td>'.
                        '<td style="padding-left: 10px">'.$partOne->focus0->name.'</td>'.
                        '<td style="padding-left: 10px">'.$team->name.'</td>'.
                        '<td><h5>'.Html::a($partFiles->filename, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $partFiles->filename, 'type' => 'participants'])).'</h5></td>'.
                        '<td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['foreign-event/update-participant', 'id' => $partOne->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td>'.
                        '<td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['foreign-event/delete-participant', 'id' => $partOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 50, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelParticipants[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items" style="padding: 0; margin: 0"><!-- widgetContainer -->
                    <?php foreach ($modelParticipants as $i => $modelParticipantsOne):
                        ?>
                        <div class="item panel panel-default" style="padding: 0; margin: 0"><!-- widgetBody -->
                            <div class="panel-heading" style="padding: 0; margin: 0">
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?php
                                    $people = \app\models\work\ForeignEventParticipantsWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО участника');
                                    $branchs = \app\models\work\BranchWork::find()->where(['!=', 'id', '5'])->orderBy(['id' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($branchs, 'id', 'name');
                                    echo '<div class="'.$i.'">';
                                    echo $form->field($modelParticipantsOne, "[{$i}]branch[]")->checkboxList(
                                        $items, ['item' => function ($index, $label, $name, $checked, $value) {
                                        return
                                            '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                                                        <label for="branch-'. $index .'">
                                                            <input onclick="ClickBranch(this, '.$index.')" class="check_branch" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                                            '. $label .'
                                                        </label>
                                                    </div>';
                                    }, 'class' => 'base'])->label('<u>Отдел(-ы)</u>');
                                    echo '</div>';
                                    ?>

                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?php
                                    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]teacher")->dropDownList($items,$params)->label('ФИО педагогов');
                                    echo $form->field($modelParticipantsOne, "[{$i}]teacher2")->dropDownList($items,$params)->label(false);
                                    $focuses = \app\models\work\FocusWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($focuses,'id','name');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]focus")->dropDownList($items,$params)->label('Направленность');
                                    $realizes = \app\models\work\AllowRemoteWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($realizes,'id','name');
                                    $params = [
                                        //'prompt' => ''
                                        'disabled' => true,
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]allow_remote_id")->dropDownList($items,$params)->label('Форма реализации');
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?= $form->field($modelParticipantsOne, "[{$i}]file")->fileInput()->label('Представленные материалы') ?>
                                    <?php
                                    $people = \app\models\work\ParticipantFilesWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'filename','filename');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]file")->dropDownList($items,$params)->label(false);
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?= $form->field($modelParticipantsOne, "[{$i}]team")->label('В составе команды'); ?>
                                </div>
                            </div>
                            <div class="panel-body" style="padding: 0; margin: 0"></div>

                        </div>
                    <?php
                    endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'min_participants_age')->textInput() ?>

    <?= $form->field($model, 'max_participants_age')->textInput() ?>



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
            $parts = \app\models\work\ParticipantAchievementWork::find()->where(['foreign_event_id' => $model->id])->all();
            if ($parts != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Участник</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Достижение</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Номер сертификата</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Номинация</b></h4></td></tr>';
                foreach ($parts as $partOne) {
                    $partOnePeople = \app\models\work\ForeignEventParticipantsWork::find()->where(['id' => $partOne->participant_id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.$partOnePeople->shortName.'</h4></td><td style="padding-left: 20px"><h4>'.$partOne->achievment.'</h4></td><td style="padding-left: 20px"><h4>'.$partOne->cert_number.'</h4></td><td style="padding-left: 20px"><h4>'.$partOne->nomination.'</h4></td>'.'<td>&nbsp;'.Html::a('Редактировать', \yii\helpers\Url::to(['foreign-event/update-achievement', 'id' => $partOne->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td>'.'<td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['foreign-event/delete-achievement', 'id' => $partOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
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
                            <div class="col-xs-4">
                                <?php
                                $parts = \app\models\work\TeacherParticipantWork::find()->where(['foreign_event_id' => $model->id])->all();
                                $newParts = [];
                                foreach ($parts as $part) $newParts[] = $part->participant_id;
                                $people = \app\models\work\ForeignEventParticipantsWork::find()->where(['in', 'id', $newParts])->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelAchievementOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО участника');

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
                                <?php

                                echo $form->field($modelAchievementOne, "[{$i}]nomination")->textInput();

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

                                echo $form->field($modelAchievementOne, "[{$i}]winner")->checkbox();

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
        $orders = \app\models\work\DocumentOrderWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
        $params = [
            'prompt' => '--',
        ];
        echo $form->field($model, 'order_business_trip_id')->dropDownList($items,$params);

        ?>
    </div>

    <?php
    $orders = \app\models\work\DocumentOrderWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [
        'prompt' => '--',
    ];
    echo $form->field($model, 'order_participation_id')->dropDownList($items,$params);

    ?>

    <?php
    $orders = \app\models\work\DocumentOrderWork::find()->all();
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
