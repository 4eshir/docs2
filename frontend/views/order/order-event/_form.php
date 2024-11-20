<?php
use app\components\DynamicWidget;
use frontend\models\work\general\PeopleWork;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model */
/* @var $people */
/* @var $scanFile */
/* @var $docFiles */
/* @var $teamList */
/* @var $awardList */
/* @var $modelResponsiblePeople */
/* @var $foreignEventTable */
/* @var $teamTable */
/* @var $awardTable */
?>
<style>
    .bordered-div {
        border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
    }
    .act-team-participant {
    }
    .act-personal-participant {
    }
</style>
<script>
    window.onload = function() {
        var actsDiv = document.getElementById('acts');
        var commandsDiv = document.getElementById('commandsDiv');
        actsDiv.style.pointerEvents = 'none'; // Блокируем ввод
        actsDiv.style.opacity = '0.5'; // Уменьшаем непрозрачность
        commandsDiv.style.pointerEvents = 'auto'; // Разблокируем ввод
        commandsDiv.style.opacity = '1'; // Восстанавливаем непрозрачность
    };
</script>
<div class="order-main-form">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form-new']); ?>
    <?= $form->field($model, 'order_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]])->label('Дата приказа') ?>
    <?= $form->field($model, 'order_number')->dropDownList(Yii::$app->nomenclature->getList(), ['prompt' => '---'])->label('Код и описание номенклатуры') ?>
    <div class="bordered-div">
        <h4>
            Информация для создания карточки учета достижений
        </h4>
        <?= $form->field($model, 'eventName')->textInput()->label('Название мероприятия') ?>
        <div id="organizer">
            <?php
            $params = [
                'id' => 'organizer',
                'class' => 'form-control pos',
                'prompt' => '---',
            ];
            echo $form
                ->field($model, 'organizer_id')
                ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                ->label('Организатор');
            ?>
        </div>
        <?= $form->field($model, 'dateBegin')->widget(DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]])->label('Дата начала') ?>
        <?= $form->field($model, 'dateEnd')->widget(DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]])->label('Дата окончания') ?>
        <?= $form->field($model, 'city')->textInput()->label('Город') ?>
        <?= $form->field($model, 'eventWay')->dropDownList(Yii::$app->eventWay->getList(), ['prompt' => '---'])
            ->label('Формат проведения') ?>
        <?= $form->field($model, 'eventLevel')->dropDownList(Yii::$app->eventLevel->getList(), ['prompt' => '---'])
            ->label('Уровень') ?>
        <?= $form->field($model, 'minister')->checkbox()->label('Входит в перечень Минпросвещения РФ') ?>
        <?= $form->field($model, 'minAge')->textInput()->label('Мин. возраст участников (лет)') ?>
        <?= $form->field($model, 'maxAge')->textInput()->label('Макс. возраст участников (лет)') ?>
        <?= $form->field($model, 'keyEventWords')->textInput()->label('Ключевые слова') ?>
    </div>
    <?= $form->field($model, 'order_name')->textInput()->label('Наименование приказа') ?>
    <div id="bring_id">
        <?php
        $params = [
            'id' => 'bring',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'bring_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Проект вносит');
        ?>
    </div>
    <div id="executor_id">
        <?php
        $params = [
            'id' => 'executor',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id')
            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
            ->label('Кто исполняет');
        ?>
    </div>
    <div class="bordered-div">
        <?php DynamicWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'model' => $model,
            'formId' => 'dynamic-form',
            'formFields' => ['order_name'],
        ]); ?>
        <div class="container-items">
            <h5 class="panel-title pull-left">Ответственные</h5><!-- widgetBody -->
            <div class="pull-right">
                <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus">+</span></button>
            </div>
            <div class="item panel panel-default" id = "item"><!-- widgetItem -->
                <button type="button" class="remove-item btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus">-</span></button>
                <div class="panel-heading">
                    <div class="clearfix"></div>
                </div>
                <div class = "form-label">
                    <div class="panel-body">
                        <?php
                        $params = [
                            'id' => 'responsible',
                            'class' => 'form-control pos',
                            'prompt' => '---',
                        ];
                        echo $form
                            ->field($model, 'responsible_id[]')
                            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                            ->label('Ответственные');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        DynamicWidget::end()
        ?>
    </div>
    <?php if (strlen($modelResponsiblePeople) > 10): ?>
        <?= $modelResponsiblePeople; ?>
    <?php endif; ?>
    <div class="bordered-div">
        <h4>Дополнительная информация для генерации приказа</h4>
        <?= $form->field($model, 'purpose')->radioList([
            '0' => 'выявления, формирования, поддержки и развития способностей и талантов у детей и молодежи 
            на территории Астраханской области, оказания содействия в получении ими дополнительного образования,
             в том числе образования в области искусств, естественных наук, физической культуры и спорта,
              а также обеспечения организации их свободного времени (досуга) и отдыха',
            '1' => 'удовлетворения образовательных и профессиональных потребностей, профессионального развития человека,
             обеспечения соответствия его квалификации меняющимся условиям профессиональной деятельности и социальной среды,
              совершенствования и (или) получения новой компетенции, необходимой для профессиональной деятельности,
               и (или) повышения профессионального уровня в рамках имеющейся квалификации',
            '2' => ' участия в формировании образовательной политики Астраханской области в области выявления,
             сопровождения и дальнейшего развития проявивших выдающиеся способности детей и молодежи в соответствии 
             с задачами социально-экономического, научно-технологического, промышленного 
             и пространственного развития Астраханской области',
        ], ['itemOptions' => ['class' => 'radio-inline']])->label('Уставная цель') ?>
        <br>
        <?= $form->field($model, 'docEvent')->radioList([
            '0' => 'Отсутствует',
            '1' => 'Регламент',
            '2' => 'Письмо',
            '3' => 'Положение',
        ], ['itemOptions' => ['class' => 'radio-inline']])->label('Документ о мероприятии') ?>
        <div id="extra_resp_info_id">
            <?php
            $params = [
                'id' => 'extra_resp_info',
                'class' => 'form-control pos',
                'prompt' => '---',
            ];
            echo $form
                ->field($model, 'respPeopleInfo')
                ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                ->label('Ответственный за сбор и предоставление информации');
            ?>
        </div>
        <?= $form->field($model, 'timeProvisionDay')->textInput()->label('Срок предоставления информации (в днях)') ?>
        <div id="extra_resp_insert_id">
            <?php
            $params = [
                'id' => 'extra_resp_insert',
                'class' => 'form-control pos',
                'prompt' => '---',
            ];
            echo $form
                ->field($model, 'extraRespInsert')
                ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                ->label('Ответственный за внесение в ЦСХД');
            ?>
        </div>
        <?= $form->field($model, 'timeInsertDay')->textInput()->label('Срок внесения информации (в днях)') ?>
        <div id="extra_resp_method_id">
            <?php
            $params = [
                'id' => 'extra_resp_method',
                'class' => 'form-control pos',
                'prompt' => '---',
            ];
            echo $form
                ->field($model, 'extraRespMethod')
                ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                ->label('Ответственный за методологический контроль');
            ?>
        </div>
        <div id="extra_resp_info_stuff_id">
            <?php
            $params = [
                'id' => 'extra_resp_info_stuff',
                'class' => 'form-control pos',
                'prompt' => '---',
            ];
            echo $form
                ->field($model, 'extraRespInfoStuff')
                ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                ->label('Ответственный за информирование работников');
            ?>
        </div>
    </div>
    <div class="bordered-div" id = "commands">
        <h4>Номинации и команды</h4>
        <div>
            <div class="container">
                <?php
                echo $form->field($model, 'team')->textInput(['id' => 'teamInput', 'class' => 'form-control pos', 'placeholder' => 'Название команды'])->label('Команды');
                ?>
                <button type="button" onclick="addToList('teamInput', 'teamList')">Добавить</button>
            </div>
            <div id="teamListContainer"></div>
            <?php if (strlen($teamTable) > 10): ?>
                <?= $teamTable; ?>
            <?php endif; ?>
            <div class="container">
                <?php
                $params = [
                    'id' => 'nominationInput',
                    'class' => 'form-control pos',
                    'prompt' => '---',
                ];
                echo $form->field($model, 'award')->textInput(['id' => 'nominationInput', 'class' => 'form-control pos', 'placeholder' => 'Номинация'])->label('Номинации');
                ?>
                <button type="button" onclick="addToList('nominationInput', 'nominationList')">Добавить</button>
            </div>
            <div id="nominationListContainer"></div>
            <!-- Скрытые поля для отправки массивов -->
            <div id="hiddenFieldsContainer"></div>
            <?php if (strlen($awardTable) > 10): ?>
                <?= $awardTable; ?>
            <?php endif; ?>
        </div>
    </div>
    <?= Html::button('Перейти к заполнению участников мероприятия', [
        'class' => 'btn btn-secondary',
        'type' => 'button',
        'id' => 'toggle-button',
    ]) ?>
    <div class="bordered-div" id = "acts">
        <h4>Акты участия</h4>
        <?php
        DynamicWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper',
            'widgetBody' => '.container-items',
            'widgetItem' => '.item',
            'model' => $model,
            'formId' => 'dynamic-form',
            'formFields' => ['order_name'],
        ]); ?>
        <div class="container-items">
            <h5 class="panel-title pull-left">
            </h5><!-- widgetBody -->
            <div class="pull-right">
                <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
            </div>
            <div class="item panel panel-default" id = "item-1" hidden><!-- widgetItem -->
                <button type="button" class="remove-item btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus"></span></button>
                <div class="panel-heading">
                    <div class="clearfix"></div>
                </div>
                <div class = "form-label"
                <label>
                    <input type="radio" class="type-participant" id="type-participant" onchange="checkType(this.name)"/>
                    Личное участие
                    <input type="radio" class="personal-type-participant" id="type-participant" onchange="checkSecondType(this.name)"/>
                    Командное участие
                </label>
                <div class="panel-body">
                    <div id="personal-participant" class="col-xs-4 act-personal-participant" hidden>
                        <div class = "bordered-div">
                            <h4>Личное участие</h4>
                            <div id = "person-participant-people">
                                <?php
                                $params = [
                                    'id' => 'participant',
                                    'class' => 'form-control pos',
                                    'prompt' => '---'
                                ];
                                echo $form
                                    ->field($model, '[personal][]participant_id[]')
                                    ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                                    ->label('ФИО участника');
                                ?>
                            </div>
                        </div>

                        <?php
                        $params = [
                            'id' => 'branch',
                            'class' => 'form-control pos',
                            'prompt' => '---',
                        ];
                        echo $form
                            ->field($model, '[personal][]branch[]')
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
                            ->field($model, '[personal][]teacher_id[]')
                            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                            ->label('ФИО учителя');
                        echo $form
                            ->field($model, '[personal][]teacher2_id[]')
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
                            ->field($model, '[personal][]focus[]')
                            ->dropDownList(Yii::$app->focus->getList(), $params)
                            ->label('Направленность');
                        ?>
                        <?= $form->field($model, '[personal][]formRealization[]')->dropDownList(Yii::$app->eventWay->getList(), ['prompt' => '---'])
                            ->label('Форма реализации') ?>

                        Представленные материалы<br>
                        <?= $form->field($model, 'actFiles[]')->fileInput()->label('Представленные материалы') ?>
                        <div class="container nomination-dropdown-list">
                            <?php
                            $params = [
                                'id' => 'nominationDropdown',
                                'class' => 'form-control pos nominationDropDownList',
                                'prompt' => '--- Выберите номинацию ---',
                            ];
                            echo $form->field($model, '[personal][]nominationList[]')->dropDownList([], $params)->label('Выберите номинацию');
                            ?>
                        </div>
                        <!-- Выпадающий список для команд -->
                        <div class="container team-dropdown-list" hidden>
                            В составе команды<br>
                            <?php
                            $params = [
                                'id' => 'teamDropdown',
                                'class' => 'form-control pos teamDropDownList',
                                'prompt' => '--- Выберите команду ---',
                            ];
                            echo $form->field($model, '[personal][]teamList[]')->dropDownList([], $params)->label('Выберите команду');
                            ?>
                        </div>
                    </div>
                    <div id="team-participant" class="col-xs-4 bordered-div act-team-participant" hidden>
                        <div>
                            <h4>Командное участие</h4>
                        </div>
                        <div id = "team-participant-people">
                            <?php
                            $params = [
                                'id' => 'participant-select-1',
                                'class' => 'form-control pos participant-select',
                                'prompt' => '---',
                                'multiple' => true // Устанавливаем параметр multiple в true
                            ];
                            echo $form
                                ->field($model, '[part][]participant_id[]')
                                ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                                ->label('ФИО участника');
                            ?>
                        </div>
                        <?php
                        $params = [
                            'id' => 'branch',
                            'class' => 'form-control pos',
                            'prompt' => '---',
                        ];
                        echo $form
                            ->field($model, '[part][]branch[]')
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
                            ->field($model, '[part][]teacher_id[]')
                            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                            ->label('ФИО учителя');
                        echo $form
                            ->field($model, '[part][]teacher2_id[]')
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
                            ->field($model, '[part][]focus[]')
                            ->dropDownList(Yii::$app->focus->getList(), $params)
                            ->label('Направленность');
                        ?>
                        <?= $form->field($model, '[part][]formRealization[]')->dropDownList(Yii::$app->eventWay->getList(), ['prompt' => '---'])
                            ->label('Форма реализации') ?>
                        Представленные материалы<br>
                        <?= $form->field($model, 'actFiles[]')->fileInput()->label('Представленные материалы') ?>
                        <div class="container nomination-dropdown-list">
                            <?php
                            $params = [
                                'id' => 'nominationDropdown',
                                'class' => 'form-control pos nominationDropDownList',
                                'prompt' => '--- Выберите номинацию ---',
                            ];
                            echo $form->field($model, '[part][]nominationList[]')->dropDownList([], $params)->label('Выберите номинацию');
                            ?>
                        </div>
                        <!-- Выпадающий список для команд -->
                        <div class="container team-dropdown-list">
                            <br>
                            <?php
                            $params = [
                                'id' => 'teamDropdown',
                                'class' => 'form-control pos teamDropDownList',
                                'prompt' => '--- Выберите команду ---',
                            ];
                            echo $form->field($model, '[part][]teamList[]')->dropDownList([], $params)->label('Выберите команду');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    DynamicWidget::end()
    ?>
</div>
<?php if (strlen($foreignEventTable) > 50): ?>
    <?= $foreignEventTable; ?>
<?php endif; ?>
<?= $form->field($model, 'key_words')->textInput()->label('Ключевые слова') ?>
<?= $form->field($model, 'scanFile')->fileInput()->label('Скан документа') ?>
<?php if (strlen($scanFile) > 10): ?>
    <?= $scanFile; ?>
<?php endif; ?>

<?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

<?php if (strlen($docFiles) > 10): ?>
    <?= $docFiles; ?>
<?php endif; ?>
<div class="form-group">
    <?= Html::submitButton('Сохранить', [
        'class' => 'btn btn-success',
        'onclick' => 'prepareAndSubmit();' // Подготовка скрытых полей перед отправкой
    ]) ?>
    <?php ActiveForm::end(); ?>
</div>
<script>
    function checkType(chkBoxName) {
        var participantNumber = chkBoxName.split('-')[1]; // Разделяем строку и берем номер
        var teamDiv = document.getElementById(`act-team-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var personDiv = document.getElementById(`act-personal-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var teamDropdownList = document.getElementById(`team-dropdown-list-${participantNumber}`);
        teamDiv.hidden = true;
        personDiv.hidden = false;
       // teamDropdownList.hidden = true;

    }
    function checkSecondType(chkBoxName) {
        var participantNumber = chkBoxName.split('-')[1]; // Разделяем строку и берем номер
        var teamDiv = document.getElementById(`act-team-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var personDiv = document.getElementById(`act-personal-participant-${participantNumber}`); // Получаем соответствующий div по ID
        var teamDropdownList = document.getElementById(`team-dropdown-list-${participantNumber}`);
        teamDiv.hidden = false;
        personDiv.hidden = true;
       // teamDropdownList.hidden = false;
    }
</script>
<script>
    function optionExists(dropdown, value) {
        // Проверяем, существует ли уже опция с заданным значением
        return Array.from(dropdown.options).some(option => option.value === value);
    }

    let teamList = []; // Temporary storage
    let nominationList = []; // Temporary storage
    // Функция для обновления списка
    function updateList() {
        const teamListContainer = document.getElementById('teamListContainer');
        const nominationListContainer = document.getElementById('nominationListContainer');
        teamListContainer.innerHTML = '';
        nominationListContainer.innerHTML = '';
        const teamDropdown = document.getElementsByClassName('teamDropDownList');
        Array.from(teamDropdown).forEach(dropdown => {
            while (dropdown.firstChild) {
                dropdown.removeChild(dropdown.firstChild);
            }

        });
        teamDropdown.innerHTML = '<option value="">--- Выберите команду ---</option>'; // Сброс
        teamList.forEach((team, index) => {
            const listContainer = createListItem(team, index, 'teamList');
            teamListContainer.appendChild(listContainer);
            // Добавление команды в выпадающий список
            const dropdownOption = document.createElement('option');
            dropdownOption.value = team;
            dropdownOption.textContent = team;
            Array.from(teamDropdown).forEach(dropdown => {
                dropdown.appendChild(dropdownOption.cloneNode(true)); // Клонируем опцию для каждого dropdown
            });
            //teamDropdown.appendChild(dropdownOption);
        });
        // Обновляем список номинаций
        const nominationDropdown = document.getElementsByClassName('nominationDropDownList');
        Array.from(nominationDropdown).forEach(dropdown => {
            while (dropdown.firstChild) {
                dropdown.removeChild(dropdown.firstChild);
            }
        });
        nominationDropdown.innerHTML = '<option value="">--- Выберите номинацию ---</option>'; // Сброс
        nominationList.forEach((nomination, index) => {
            const listContainer = createListItem(nomination, index, 'nominationList');
            nominationListContainer.appendChild(listContainer);
            const nominationOption = document.createElement('option');
            nominationOption.value = nomination;
            nominationOption.textContent = nomination;
            Array.from(nominationDropdown).forEach(dropdown => {
                dropdown.appendChild(nominationOption.cloneNode(true)); // Клонируем опцию для каждого dropdown
            });
        });
    }
    // Остальные функции (createListItem, addToList, deleteItem, prepareAndSubmit) остаются без изменений...
    // Функция для создания элемента списка
    function createListItem(item, index, type) {
        const listContainer = document.createElement('div');
        listContainer.classList.add('list-container');

        const itemText = document.createElement('span');
        itemText.textContent = item;
        listContainer.appendChild(itemText);

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Удалить';
        deleteButton.classList.add('delete-btn');
        deleteButton.onclick = function() {
            deleteItem(index, type);
        };
        listContainer.appendChild(deleteButton);

        return listContainer;
    }
    // Функция добавления элемента в список
    function addToList(inputId, listType) {
        const inputField = document.getElementById(inputId);
        const inputText = inputField.value.trim();

        if (inputText !== "") {
            if (listType === 'teamList') {
                teamList.push(inputText);
            } else if (listType === 'nominationList') {
                nominationList.push(inputText);
            }

            updateList(); // Обновляем отображение
            inputField.value = ""; // Очищаем текстовое поле
        }
    }
    // Функция удаления элемента из списка
    function deleteItem(index, listType) {
        if (listType === 'teamList') {
            teamList.splice(index, 1);
        } else if (listType === 'nominationList') {
            nominationList.splice(index, 1);
        }
        updateList(); // Обновляем отображение
    }
    // Функция подготовки и отправки формы
    function prepareAndSubmit() {
        const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');
        hiddenFieldsContainer.innerHTML = ''; // Удаляем старые скрытые поля

        // Добавляем команды в скрытые поля
        teamList.forEach(team => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'teams[]'; // Обратите внимание на использование 'teams[]'
            input.value = team;
            hiddenFieldsContainer.appendChild(input);
        });

        // Добавляем номинации в скрытые поля
        nominationList.forEach(nomination => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'nominations[]'; // Аналогично для номинаций
            input.value = nomination;
            hiddenFieldsContainer.appendChild(input);
        });
    }
    // Первоначальное обновление списка
    updateList();
</script>
<script>
    document.getElementById('toggle-button').addEventListener('click', function() {
        const actsDiv = document.getElementById('acts');
        const commandsDiv = document.getElementById('commands');

        // Переключаем текст кнопки
        if (this.innerText === 'Перейти к заполнению участников мероприятия') {
            this.innerText = 'Вернуться к заполнению команд и номинаций';
            commandsDiv.style.pointerEvents = 'none'; // Блокируем ввод
            commandsDiv.style.opacity = '0.5'; // Уменьшаем непрозрачность
            actsDiv.style.pointerEvents = 'auto'; // Разблокируем ввод
            actsDiv.style.opacity = '1';  // Восстанавливаем непрозрачность
        } else {
            this.innerText = 'Перейти к заполнению участников мероприятия';
            //
            actsDiv.style.pointerEvents = 'none'; // Блокируем ввод
            actsDiv.style.opacity = '0.5'; // Уменьшаем непрозрачность
            commandsDiv.style.pointerEvents = 'auto'; // Разблокируем ввод
            commandsDiv.style.opacity = '1';  // Восстанавливаем непрозрачность
        }
    });
</script>
<script>
    // Ждем загрузки DOM
    function updateCheckboxNames() {
        const checkboxes = document.querySelectorAll('input[type="radio"].type-participant');
        checkboxes.forEach((checkbox, index) => {
            checkbox.name = `participant-${index + 1}`;
        });
        requestAnimationFrame(updateCheckboxNames);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateCheckboxNames);
</script>
<script>
    // Ждем загрузки DOM
    function updatePersonalCheckboxNames() {
        const checkboxes2 = document.querySelectorAll('input[type="radio"].personal-type-participant');
        checkboxes2.forEach((checkbox2, index) => {
            checkbox2.name = `participant-${index + 1}`;
        });
        requestAnimationFrame(updatePersonalCheckboxNames);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updatePersonalCheckboxNames);
</script>
<script>
    function updateDivNames() {
        const divs = document.querySelectorAll('div.act-team-participant');
        divs.forEach((div, index) => {
            div.id = `act-team-participant-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateDivNames);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateDivNames);
</script>
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
<script>
    function updateNominationDropdownList() {
        const divs = document.querySelectorAll('div.nomination-dropdown-list');
        divs.forEach((div, index) => {
            div.id = `nomination-dropdown-list-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateNominationDropdownList);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateNominationDropdownList);
</script>
<script>
    function updateDivPersonalNames() {
        const divs = document.querySelectorAll('div.act-personal-participant');
        divs.forEach((div, index) => {
            div.id = `act-personal-participant-${index + 1}`; // Уникальное имя с индексом
        });
        requestAnimationFrame(updateDivPersonalNames);
    }
    // Запускаем первую функцию вызова
    requestAnimationFrame(updateDivPersonalNames);
</script>