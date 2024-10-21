<?php
use app\components\DynamicWidget;
use app\models\work\order\OrderMainWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* @var $model \app\models\work\order\OrderEventWork */
/* @var $people */
/* @var $scanFile */
/* @var $docFiles */
?>
<style>
    .bordered-div {
    border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
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
    <?php $form = ActiveForm::begin(); ?>
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
                <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
            </div>
            <div class="item panel panel-default" id = "item"><!-- widgetItem -->
                <button type="button" class="remove-item btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus"></span></button>
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
    </div>
    <?= Html::button('Перейти к заполнению участников мероприятия', [
            'class' => 'btn btn-secondary',
            'type' => 'button',
            'id' => 'toggle-button',
    ]) ?>
    <div class="bordered-div" id = "acts">
        <h4>Акты участия</h4>
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
                <button type="button" class="add-item btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
            </div>
            <div class="item panel panel-default" id = "item"><!-- widgetItem -->
                <button type="button" class="remove-item btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus"></span></button>
                <div class="panel-heading">
                    <div class="clearfix"></div>
                </div>
                <div class = "form-label">
                    <div class="panel-body">
                        <?php
                        $params = [
                            'id' => 'participant',
                            'class' => 'form-control pos',
                            'prompt' => '---',
                        ];
                        echo $form
                            ->field($model, 'participant_id[]')
                            ->dropDownList(ArrayHelper::map($people, 'id', 'fullFio'), $params)
                            ->label('ФИО участника');
                        ?>
                        <?php
                        $params = [
                            'id' => 'branch',
                            'class' => 'form-control pos',
                            'prompt' => '---',
                        ];
                        echo $form
                            ->field($model, 'branch[]')
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
                            ->field($model, 'teacher_id[]')
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
                            ->field($model, 'focus[]')
                            ->dropDownList(Yii::$app->focus->getList(), $params)
                            ->label('Направленность');
                        ?>
                        <?= $form->field($model, 'formRealization')->dropDownList(Yii::$app->eventWay->getList(), ['prompt' => '---'])
                            ->label('Форма реализации') ?>
                        <h3>
                            Представленные материалы<br>
                            В составе команды<br>
                            Номинация
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <?php
        DynamicWidget::end()
        ?>
    </div>
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
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
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












