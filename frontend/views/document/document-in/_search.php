<?php

///*$form->field($model, 'local_date')->widget(DatePicker::class, [
//        'dateFormat' => 'php:d.m.Y',
//        'language' => 'ru',
//        'options' => [
//            'placeholder' => 'Дата',
//            'class'=> 'form-control',
//            'autocomplete'=>'off'
//        ],
//        'clientOptions' => [
//            'changeMonth' => true,
//            'changeYear' => true,
//            'yearRange' => '2000:2100',
//        ]])->label('Дата поступления документа')*/

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchDocumentIn */

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>
<div class="filter-panel" id="filterPanel">
    <h3>
        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path d="M9 12L4 4H15M20 4L15 12V21L9 18V16" stroke="#009580" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg> Фильтры поиска:
    </h3>
    <div class="filter-date">
        <div class="flexx">
            <div class="filter-input">
                <?= $form->field($searchModel, 'startDateSearch')->widget(DatePicker::class, [
                    'dateFormat' => 'php:d.m.Y',
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Дата документа с',
                        'class'=> 'form-control',
                        'autocomplete'=>'off',
                    ],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '2018:2030',
                    ]])->label(false); ?>
            </div>
            <div class="filter-input">
                <?= $form->field($searchModel, 'finishDateSearch')->widget(DatePicker::class, [
                    'dateFormat' => 'php:d.m.Y',
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Дата документа по',
                        'class'=> 'form-control',
                        'autocomplete'=>'off',
                    ],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '2018:2030',
                    ]])->label(false); ?>
            </div>
            <div class="filter-input">
                <?= $form->field($searchModel, 'number')->textInput(['placeholder' => 'Номер документа'])->label(false); ?>
            </div>
        </div>
        <div class="flexx">
            <div class="filter-input">
                <?= $form->field($searchModel, 'documentTheme')->textInput(['placeholder' => 'Тема документа'])->label(false); ?>
            </div>
            <div class="filter-input">
                <?= $form->field($searchModel, 'key_words')->textInput(['placeholder' => 'Ключевые слова'])->label(false); ?>
            </div>
            <div class="filter-input">
                <?= $form->field($searchModel, 'correspondentName')->textInput(['placeholder' => 'Корреспондент'])->label(false); ?>
            </div>
        </div>
        <div class="flexx">
            <div class="filter-input">
                <?= $form->field($searchModel, 'executorName')->textInput(['placeholder' => 'Исполнитель'])->label(false); ?>
            </div>
            <div class="filter-input">
                <?= $form->field($searchModel, 'sendMethodName')
                    ->dropDownList(Yii::$app->sendMethods->getList(), [
                        'prompt' => 'Способ получения',
                        'options' => [
                            $searchModel->sendMethodName => ['selected' => true],
                        ],
                    ])
                    ->label(false);
                ?>
            </div>
            <div class="filter-input">
                <?= $form->field($searchModel, 'status')
                    ->dropDownList(Yii::$app->documentStatus->getList(), [
                        'prompt' => 'Статус документа',
                        'options' => [
                            $searchModel->status => ['selected' => true],
                        ],
                    ])
                    ->label(false);
                ?>
            </div>
        </div>
        <div class="form-group-button">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']); ?>
            <?= Html::resetButton('Очистить', ['class' => 'btn btn-secondary', 'style' => 'font-weight: 500;']); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>