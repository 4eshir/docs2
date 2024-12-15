<?php

/* @var $modelActs */
/* @var $this yii\web\View */
/* @var $people */
/* @var $teams */
/* @var $nominations */
/* @var $defaultTeam */
/* @var $act */
/* @var $tables */
use kartik\select2\Select2;
use kidzen\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
<script>
    function displayDetails()
    {
        var elem = document.getElementById('documentorderwork-supplement-compliance_document').getElementsByTagName('input');
        var details = document.getElementById('details');

        if (elem[0].checked)
            details.style.display = "none";
        else
            details.style.display = "block";

        let item = [1, 2, 3];
        item.forEach((element) => {
            if (elem[element].checked)
                details.childNodes[2*element-1].hidden = false;
            else
                details.childNodes[2*element-1].hidden = true;
        });
    }

    let listId = 'nomDdList'; //айди выпадающего списка, в который будут добавлены номинации
    let listId2 = 'teamDdList'; //айди выпадающего списка, в который будут добавлены команды

    //let nominations = [];
    //let team = [];
    let team = <?php echo json_encode($teams); ?>;
    let nominations = <?php echo json_encode($nominations); ?>;
    let defaultNomination = <?php echo json_encode($modelActs[0]->nomination); ?>;
    let defaultTeam = <?php echo json_encode($defaultTeam); ?>;
    window.onload = function(){
        if (nominations != null) {
            FinishNom();
            document.getElementById('nominationDropdown').value = defaultNomination;
        }
        if (team != null) {
            FinishTeam();
            document.getElementById('teamDropdown').value = defaultTeam; // Установите значение по умолчанию
        }


        if (document.getElementById('documentorderwork-order_date').value === '')
        {
            document.getElementById('documentorderwork-supplement-foreign_event_goals_id').childNodes[0].childNodes[0].checked = true;
            document.getElementById('documentorderwork-supplement-compliance_document').childNodes[0].childNodes[0].checked = true;
        }
        document.getElementsByClassName('form-group field-documentorderwork-foreign_event-is_minpros')[0].childNodes[4].style.color = 'white';
        displayDetails();
    }
    function handleParticipationChange(radio) {
        const name = radio.name;
        const index = name.match(/\[(\d+)\]/);
        if (index) {
            let extractedIndex = index[1];
            extractedIndex++;
            var teamDropdownList = document.getElementById(`team-dropdown-list-${extractedIndex}`);
            if (radio.value === '0') {
                teamDropdownList.hidden = true;
            } else if (radio.value === '1') {
                teamDropdownList.hidden = false;
            }
        }
    }  function FinishNom()
    {
        let elem = document.getElementsByClassName(listId);

        for (let z = 0; z < elem.length; z++)
        {
            while (elem[z].options.length) {
                elem[z].options[0] = null;
            }

            elem[z].appendChild(new Option("--", 'NULL'));

            for (let i = 0; i < nominations.length; i++)
            {
                var option = document.createElement('option');
                option.value = nominations[i];
                option.innerHTML = nominations[i];
                elem[z].appendChild(option);
            }
        }
    }

    function FinishTeam()
    {
        let elem = document.getElementsByClassName(listId2);

        for (let z = 0; z < elem.length; z++)
        {
            while (elem[z].options.length) {
                elem[z].options[0] = null;
            }

            elem[z].appendChild(new Option("--", 'NULL'));

            for (let i = 0; i < team.length; i++)
            {
                var option = document.createElement('option');
                option.value = team[i];
                option.innerHTML = team[i];
                elem[z].appendChild(option);
            }
        }
    }
</script>
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
                                <?= $tables ?>
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
        <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-success',
            'onclick' => 'prepareAndSubmit();' // Подготовка скрытых полей перед отправкой
        ]) ?>
    <?php $form = ActiveForm::end(); ?>
</div>
<script>
    function updateTeamDropdownList() {
        const divs = document.querySelectorAll('div.team-dropdown-list');
        divs.forEach((div, index) => {
            div.id = `team-dropdown-list-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateTeamDropdownList);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateTeamDropdownList);
</script>