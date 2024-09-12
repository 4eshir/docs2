<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\work\EventScopeWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\EventWork */
/* @var $form yii\widgets\ActiveForm */
?>

<script type="text/javascript">
    window.onload = function(){
        let elem = document.getElementById('all_scopes');
        let ids = elem.innerHTML.split(' ');

        let checks = document.getElementsByClassName('sc');

        for (let i = 0; i < ids.length; i++)
            for (let j = 0; j < checks.length; j++)
                if (ids[i] == checks[j].value)
                    checks[j].setAttribute('checked', 'checked');
    }
</script>

<script src="/scripts/sisyphus/sisyphus.js"></script>
<script src="/scripts/sisyphus/sisyphus.min.js"></script>

<style type="text/css">
    .checkList{
        border: 1px solid #dddddd;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .checkBlock{
        height: 400px;
        overflow-y: scroll;
        margin-right: -15px;
        margin-bottom: -15px;
        margin-top: -15px;

        padding-top: 10px;
    }

    .checkHeader{
        background: #f5f5f5;
        border-bottom: 1px solid #dddddd;
        margin-top: -15px;
        margin-left: -15px;
        margin-right: -15px;
        margin-bottom: 15px;
        line-height: 2em;
    }

    .noPM{
        margin: 0;
        padding: 0;
        line-height: 3;
        padding-left: 15px;
    }
</style>


<div id="all_scopes" style="display: none;"><?php
    $sc = EventScopeWork::find()->where(['event_id' => $model->id])->all();
    $res = '';
    foreach ($sc as $one)
        $res .= $one->participation_scope_id.' ';
    $res = substr($res, 0, -1);
    echo $res;
    ?>
</div>

<div class="event-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата начала мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата начала мероприятия') ?>

    <?= $form->field($model, 'finish_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата окончания мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата окончания мероприятия') ?>

    <?php
    $orders = \app\models\work\EventTypeWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [];

    echo $form->field($model, 'event_type_id')->dropDownList($items,$params)->label('Тип мероприятия');

    ?>

    <?php
    $orders = \app\models\work\EventFormWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [];

    echo $form->field($model, 'event_form_id')->dropDownList($items,$params)->label('Форма мероприятия');

    ?>

    <?php
    $ways = \app\models\work\EventWayWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($ways,'id','name');
    $params = [
    ];

    echo $form->field($model, 'event_way_id')->dropDownList($items,$params)->label('Формат проведения');

    ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = \app\models\work\EventLevelWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [];

    echo $form->field($model, 'event_level_id')->dropDownList($items,$params)->label('Уровень мероприятия');

    ?>

    <div class="checkList">
        <div class="checkHeader">
            <h4 class="noPM">Сферы участия</h4>
        </div>

        <div class="checkBlock">
            <?php

            $scopes = \app\models\work\ParticipationScopeWork::find()->orderBy(['name' => SORT_ASC])->all();
            $items = \yii\helpers\ArrayHelper::map($scopes,'id','name');

            echo $form->field($model, 'scopes')->checkboxList($items, [
                'item' => function($index, $label, $name, $checked, $value) {
                return "<div 'class'='col-sm-12'><label><input class='sc' type='checkbox' {$checked} name='{$name}'value='{$value}'> {$label}</label></div>";
            }])->label(false) ?>
        </div>

    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Участники</h4></div>
            <div class="panel-body">
                <?= $form->field($model, 'childs')->textInput(['value' => $model->childs == null ? 0 : $model->childs]) ?>
                <?= $form->field($model, 'childs_rst')->textInput(['value' => $model->childs_rst == null ? 0 : $model->childs_rst]) ?>
                <?= $form->field($model, 'leftAge')->textInput(['value' => $model->leftAge == null ? 5 : $model->leftAge]) ?>
                <?= $form->field($model, 'rightAge')->textInput(['value' => $model->rightAge == null ? 18 : $model->rightAge]) ?>
                <br>
                <?= $form->field($model, 'teachers')->textInput(['value' => $model->teachers == null ? 0 : $model->teachers]) ?>
                <?= $form->field($model, 'others')->textInput(['value' => $model->others == null ? 0 : $model->others]) ?>

            </div>
        </div>
    </div>

    <?php //echo $form->field($model, 'is_federal')->checkbox() ?>

    <?php
    $orders = \app\models\work\PeopleWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','shortName');
    $params = [];

    echo $form->field($model, 'responsible_id')->dropDownList($items,$params)->label('Ответственный за мероприятие');

    ?>
    <?php
    $orders = \app\models\work\PeopleWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','shortName');
    $params = [
        'prompt' => '--',
    ];

    echo $form->field($model, 'responsible2_id')->dropDownList($items,$params)->label('Второй ответственный (опционально)');

    ?>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Мероприятие проводит</h4></div>
            <div class="panel-body">

                <?php
                $tech = \app\models\work\EventBranchWork::find()->where(['branch_id' => 2])->andWhere(['event_id' => $model->id])->all();
                $quant = \app\models\work\EventBranchWork::find()->where(['branch_id' => 1])->andWhere(['event_id' => $model->id])->all();
                $cdntt = \app\models\work\EventBranchWork::find()->where(['branch_id' => 3])->andWhere(['event_id' => $model->id])->all();
                $mobquant = \app\models\work\EventBranchWork::find()->where(['branch_id' => 4])->andWhere(['event_id' => $model->id])->all();
                $cod = \app\models\work\EventBranchWork::find()->where(['branch_id' => 7])->andWhere(['event_id' => $model->id])->all();

                $value = 'false';
                ?>
                <?php if (count($tech) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isTechnopark')->checkbox(['checked' => $value]) ?>

                <?php if (count($quant) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isQuantorium')->checkbox(['checked' => $value]) ?>

                <?php if (count($cdntt) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isCDNTT')->checkbox(['checked' => $value]) ?>

                <?php if (count($mobquant) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isMobQuant')->checkbox(['checked' => $value]) ?>

                <?php if (count($cod) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isCod')->checkbox(['checked' => $value]) ?>

            </div>
        </div>
    </div>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <?php
    $orders = \app\models\work\DocumentOrderWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [
        'prompt' => 'Нет'
    ];

    echo $form->field($model, 'order_id')->dropDownList($items,$params)->label('Приказ по мероприятию');

    ?>

    <?php
    $orders = \app\models\work\RegulationWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
    $params = [
        'prompt' => 'Нет'
    ];

    echo $form->field($model, 'regulation_id')->dropDownList($items,$params)->label('Положение по мероприятию');

    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Отчетные мероприятия</h4></div>
            <?php
            $extEvents = \app\models\work\EventsLinkWork::find()->where(['event_id' => $model->id])->all();
            if ($extEvents != null)
            {
                echo '<table>';
                foreach ($extEvents  as $extEvent) {
                    echo '<tr><td style="padding-left: 20px"><h4>"'.$extEvent->eventExternal->name.'"</h4></td> <td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['event/delete-external-event', 'id' => $extEvent->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items1', // required: css class selector
                    'widgetItem' => '.item1', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelEventsLinks[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items1" ><!-- widgetContainer -->
                    <?php foreach ($modelEventsLinks as $i => $modelEventsLink): ?>
                        <div class="item1 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Мероприятие</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelEventsLink->isNewRecord) {
                                    echo Html::activeHiddenInput($modelEventsLink, "[{$i}]id");
                                }
                                ?>
                                <div>
                                    <?php

                                    $branch = \app\models\work\EventExternalWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
                                    $params = [
                                        'prompt' => '',
                                    ];
                                    echo $form->field($modelEventsLink, "[{$i}]eventExternalName")->dropDownList($items,$params)->label('Название мероприятия');
                                    ?>

                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Связанные учебные группы</h4></div>
            <?php
            $groups = \app\models\work\EventTrainingGroupWork::find()->where(['event_id' => $model->id])->all();
            if ($groups != null)
            {
                echo '<table>';
                foreach ($groups  as $group) {
                    echo '<tr><td style="padding-left: 20px"><h4>Группа '.$group->trainingGroupWork->numberExtended.'</h4></td> <td>&nbsp;'.Html::a('Удалить', \yii\helpers\Url::to(['event/delete-group', 'id' => $group->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper2', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items2', // required: css class selector
                    'widgetItem' => '.item2', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item2', // css class
                    'deleteButton' => '.remove-item2', // css class
                    'model' => $modelGroups[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items2" ><!-- widgetContainer -->
                    <?php foreach ($modelGroups as $i => $modelGroup): ?>
                        <div class="item2 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Учебная группа</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item2 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item2 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <div>
                                    <?php

                                    $groups = \app\models\work\TrainingGroupWork::find()->where(['archive' => 0])->orderBy(['start_date' => SORT_DESC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($groups,'id','numberExtended');
                                    $params = [
                                        'prompt' => '',
                                    ];
                                    echo $form->field($modelGroup, "[{$i}]training_group_id")->dropDownList($items,$params)->label('Номер группы');
                                    ?>

                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'contains_education')->radioList(array(0 => 'Не содержит образовательных программ',
                                                                           1 => 'Содержит образовательные программы'), ['value'=>$model->contains_education ])->label('') ?>


    <?= $form->field($model, 'protocolFile[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->protocol) > 2)
    {
        $split = explode(" ", $model->protocol);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => 'protocol/'.$split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['event/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <?= $form->field($model, 'photoFiles[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->photos) > 2)
    {
        $split = explode(" ", $model->photos);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => 'photos/'.$split[$i].'+'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['event/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'photos'])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <?= $form->field($model, 'reportingFile[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->reporting_doc) > 2)
    {
        $split = explode(" ", $model->reporting_doc);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => 'reporting/'.$split[$i].'+'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['event/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'report'])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <?= $form->field($model, 'otherFiles[]')->fileInput(['multiple' => true]) ?>
    <?php
    if (strlen($model->other_files) > 2)
    {
        $split = explode(" ", $model->other_files);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => 'other/'.$split[$i].'+'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['event/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'report'])).'</td></tr>';

            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => 'other/'.$split[$i].'+'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['event/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'other'])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    $('form').sisyphus();
    
    var reloaded  = function(){alert('reload');} //страницу перезагрузили
    window.onload = function() {
      var loaded = sessionStorage.getItem('loaded');
      if(loaded) {
        reloaded();
      } else {
        sessionStorage.setItem('loaded', true);
      }
    }
</script>