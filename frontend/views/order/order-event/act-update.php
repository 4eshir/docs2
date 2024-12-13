<?php

/* @var $modelActs */
/* @var $this yii\web\View */
/* @var $people */
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
<div class = "act-participant-update">
    <div class = "bordered-div" id = "acts">
        <h3>Акты участия</h3>
        <div class="panel-body">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper_act', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items-act', // required: css class selector
                'widgetItem' => '.item-act', // required: css class
                'limit' => 20, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item-act', // css class
                'deleteButton' => '.remove-item-act', // css class
                'model' => $modelActs[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'full_name',
                ],
            ]); ?>
            <div class="container-items-act"><!-- widgetContainer -->
                <?php foreach ($modelActs as $i => $modelAct): ?>
                    <div class="item-act panel panel-default"><!-- widgetBody -->
                        <label>
                            <?=
                            $form->field($modelAct, "[{$i}]type")->radioList([
                                '0' => 'Личное участие',
                                '1' => 'Командное участие',
                            ], ['itemOptions' => ['class' => 'radio-inline', 'onclick' => 'handleParticipationChange(this)']])
                                ->label('Выберите тип участия');
                            ?>
                        </label>
                        <div class="panel-heading">
                            <h3 class="panel-title pull-left"></h3>
                            <div class="pull-right">
                                <button type="button" class="add-item-act btn btn-success btn-xs" onclick="updateOptions()"><i class="glyphicon glyphicon-plus">+</i></button>
                                <button type="button" class="remove-item-act btn btn-danger btn-xs" onclick="updateOptions()"><i class="glyphicon glyphicon-minus">-</i></button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div>
                                    <?= $form->field($modelAct, "[{$i}]participant")->widget(Select2::classname(), [
                                        'data' => ArrayHelper::map($people,'id','fullFio'),
                                        'size' => Select2::LARGE,
                                        'options' => [
                                            'prompt' => 'Выберите участника' ,
                                            'multiple' => true
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ])->label('ФИО участника'); ?>
                                </div>
                                <?php
                                $params = [
                                    'id' => 'branch',
                                    'class' => 'form-control pos',
                                    'prompt' => '---',
                                ];
                                echo $form
                                    ->field($modelAct, "[{$i}]branch")
                                    ->dropDownList(Yii::$app->branches->getList(), $params)
                                    ->label('Отделы');
                                ?>
                                <?php
                                $params = [
                                    'id' => 'teacher',
                                    'class' => 'form-control pos',
                                    'prompt' => '---',
                                ];
                                echo $form
                                    ->field($modelAct, "[{$i}]firstTeacher")
                                    ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                                    ->label('ФИО учителя');
                                echo $form
                                    ->field($modelAct, "[{$i}]secondTeacher")
                                    ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                                    ->label('ФИО учителя');
                                ?>
                                <?php
                                $params = [
                                    'id' => 'focus',
                                    'class' => 'form-control pos',
                                    'prompt' => '---',
                                ];
                                echo $form
                                    ->field($modelAct, "[{$i}]focus")
                                    ->dropDownList(Yii::$app->focus->getList(), $params)
                                    ->label('Направленность');
                                ?>
                                <?= $form->field($modelAct, "[{$i}]form")->dropDownList(Yii::$app->eventWay->getList(), ['prompt' => '---'])
                                    ->label('Форма реализации') ?>
                                <?= $form->field($modelAct, "[{$i}]actFiles")->fileInput()->label('Представленные материалы') ?>
                                <div class="container nomination-dropdown-list">
                                    <?php
                                    $params = [
                                        'id' => 'nominationDropdown',
                                        'class' => 'form-control pos nominationDropDownList nomDdList',
                                        'prompt' => '--- Выберите номинацию ---',
                                    ];
                                    echo $form->field($modelAct, "[{$i}]nomination")->dropDownList([], $params)->label('Выберите номинацию');
                                    ?>
                                </div>
                                <div class="container team-dropdown-list">
                                    В составе команды<br>
                                    <?php
                                    $params = [
                                        'id' => 'teamDropdown',
                                        'class' => 'form-control pos teamDropDownList teamDdList',
                                        'prompt' => '--- Выберите команду ---',
                                    ];
                                    echo $form->field($modelAct, "[{$i}]team")->dropDownList([], $params)->label('Выберите команду');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>
    <?php $form = ActiveForm::end(); ?>
</div>