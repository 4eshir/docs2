<?php

use common\helpers\files\FilesHelper;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrainingProgramWork */
/* @var $thematicPlan array */
/* @var $buttonsAct */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="training-program-view">

<!--<style>
    .accordion {
        background-color: #3680b1;
        color: white;
        cursor: pointer;
        padding: 8px;
        width: 100%;
        text-align: left;
        border: none;
        outline: none;
        transition: 0.4s;
        border-radius: 5px;
    }

    /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
    .active, .accordion:hover {

    }

    /* Style the accordion panel. Note: hidden by default */
    .panel {
        padding: 0 18px;
        background-color: white;
        display: none;
        overflow: hidden;
    }

    .hoverless:hover {
        cursor: default;
    }
</style>-->

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Актуальность программы
                    </div>
                    <div class="field-date">
                        <?= 'Да/Нет' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Уровень сложности
                    </div>
                    <div class="field-date">
                        <?= '1' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Возрастные ограничения
                    </div>
                    <div class="field-date">
                        <?= '7-8 лет' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Объем программы
                    </div>
                    <div class="field-date">
                        <?= '72 ак. час. по 20 мин.' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Дополнительная информация</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Направленность
                    </div>
                    <div class="field-date">
                        <?= 'Техническая' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тематическое направление
                    </div>
                    <div class="field-date">
                        <?= 'Общее интеллектуальное развитие (ОИР)' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Форма и место реализации</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма реализации
                    </div>
                    <div class="field-date">
                        <?= 'Только очная форма' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Сетевая форма обучения
                    </div>
                    <div class="field-date">
                        <?= 'Да/Нет' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Отдел(-ы) - место реализации
                    </div>
                    <div class="field-date">
                        <?= 'ЦДНТТ' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Итоговая форма контроля
                    </div>
                    <div class="field-date">
                        <?= 'Завершение с итоговой контрольной работой' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Учебно-тематический план
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Описание
                    </div>
                    <div class="field-date">
                        <?= 'Программа направлена на подготовку к перечневым олимпиадам по математике для учащихся 8 класса. Основная цель обучения – развитие математических способностей и подготовка к олимпиадам. В рамках программы ученики научатся решать математический задачи олимпиадного уровня и подготовятся к таким олимпиадам, как Всероссийская олимпиада школьников, Турнир Архимеда, Московская математическая олимпиада, олимпиада имени Эйлера и т.д. В процессе обучения школьники самостоятельно решают различные олимпиадные задачи, подробно разбирают примеры задач перечневых олимпиад.' ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Педагогический совет</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата пед. совета
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Номер протокола
                    </div>
                    <div class="field-date">
                        <?= '2023-05-15' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Составители
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Связанные группы</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= 'математика вне бюджет' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= '' ?><div>Документ программы</div></div>
                    <div class="file-block-center"><?= '' ?><div>Редактируемый документ</div></div>
                    <div class="file-block-center"><?= '' ?><div>Договор о сетевой форме</div></div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Свойства</div>
                <div class="flexx">
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Создатель карточки
                        </div>
                        <div class="field-date">
                            <?= $model->getCreatorName(); ?>
                        </div>
                    </div>
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Последний редактор
                        </div>
                        <div class="field-date">
                            <?= $model->getLastEditorName(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            ['attribute' => 'level', 'value' => function (TrainingProgramWork $model) {
                return $model->level + 1;
            }],
            'ped_council_date',
            'ped_council_number',
            ['attribute' => 'compilers', 'format' => 'html'],
            'capacity',
            'student_left_age',
            'student_right_age',
            ['attribute' => 'focus', 'value' => function (TrainingProgramWork $model) {
                return Yii::$app->focus->get($model->focus);
            }, 'format' => 'raw'],
            ['attribute' => 'fullDirectionName', 'label' => 'Тематическое направление'],
            'hour_capacity',
            ['attribute' => 'themesPlan', 'value' =>
                '<button class="accordion">Показать учебно-тематический план</button><div class="panel">'.implode('<br>', ArrayHelper::getColumn($thematicPlan, 'theme')).'</div>',
                'format' => 'raw', 'label' => 'Учебно-тематический план'],
            ['attribute' => 'branches', 'format' => 'raw'],
            ['attribute' => 'allowRemote', 'format' => 'raw'],
            ['attribute' => 'mainFile', 'value' => function (TrainingProgramWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_MAIN), 'link'));
            }, 'format' => 'raw'],
            ['attribute' => 'docFiles', 'value' => function ($model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_DOC), 'link'));
            }, 'format' => 'raw'],
            ['attribute' => 'contractFile', 'value' => function ($model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_CONTRACT), 'link'));
            }, 'format' => 'raw'],
            ['attribute' => 'certificateType', 'label' => 'Итоговая форма контроля', 'value' => function (TrainingProgramWork $model) {
                return Yii::$app->certificateType->get($model->certificate_type);
            }],
            ['attribute' => 'description', 'label' => 'Описание'],
            'key_words',
            ['attribute' => 'actual', 'value' => function($model) {return $model->actual == 0 ? 'Нет' : 'Да';}, 'label' => 'Образовательная программа актуальна'],
            //['attribute' => 'linkGroups', 'value' => '<div style="float: left; width: 20%; height: 100%; line-height: 250%">'.$model->getGroupsCount().'</div><div style="float: left; width: 80%"><button class="accordion" style="display: flex; float: left">Показать учебные группы</button><div class="panel">'.$model->getLinkGroups().'</div></div>', 'format' => 'raw', 'label' => 'Учебные группы'],
            ['attribute' => 'creatorString', 'format' => 'raw'],
            ['attribute' => 'lastUpdateString', 'format' => 'raw'],
        ],
    ]) ?>

</div>

<!--<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");

            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
</script>-->