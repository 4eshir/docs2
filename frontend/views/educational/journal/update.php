<?php

use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use frontend\forms\journal\JournalForm;
use frontend\models\work\educational\journal\VisitWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model JournalForm */
/* @var $buttonsAct */

$this->title = 'Редактирование журнала ' . $model->getTrainingGroupNumber();
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_INDEX]];
$this->params['breadcrumbs'][] = ['label' => 'Группа ' . $model->getTrainingGroupNumber(), 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = ['label' => 'Электронный журнал', 'url' => [Yii::$app->frontUrls::JOURNAL_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = $this->title;
$userData = [
    'name' => 'John Doe',
    'age' => 30,
    'email' => 'john.doe@example.com'
];
?>

<script>
    /**
     * Изменение размеров окна электронного журнала
     * @param step
     */
    function resize(step) {
        let table = document.getElementById("journal");

        if(table) {
            table.style.height = table.offsetHeight + step + "px";
        }
    }
</script>
<script>
    /**
     * Глобальные переменные
     */
    let currentIcon, IconTurnoutLink, IconNonAppearanceLink, IconDistantLink, IconDroppedLink, elements, svgData = '';
    let IconTurnout, IconNonAppearance, IconDistant, IconDropped;

    /**
     * Инициализация иконок
     */
    function init() {
        IconTurnoutLink = '<?= Url::base(true) .'/'. FilePaths::SVG_TURNOUT ?>';
        IconNonAppearanceLink = '<?= Url::base(true) .'/'. FilePaths::SVG_NON_APPEARANCE ?>';
        IconDistantLink = '<?= Url::base(true) .'/'. FilePaths::SVG_DISTANT ?>';
        IconDroppedLink = '<?= Url::base(true) .'/'. FilePaths::SVG_DROPPED ?>';
        elements = document.getElementsByTagName('input');

        saveSvgFile(IconTurnoutLink, IconNonAppearanceLink, IconDistantLink, IconDroppedLink);
        let cell = document.getElementsByClassName('attendance');
        Array.from(cell).forEach(oneCell => {
            oneCell.addEventListener('click', function () {
                clickOneCell(oneCell);
            });
        });
    }

    /**
     * Функция обновления данных по столбцам
     * @param header
     * @param columnIndex
     */
    function clickOneCellThead(header, columnIndex)
    {
        const table = header.closest('table'); // Определяем количество строк в таблице
        const rows = table.querySelectorAll('tbody tr'); // Проходим по каждой строке и обновляем соответствующую ячейку
        rows.forEach(row => {
            const cell = row.cells[columnIndex]; // Находим нужную ячейку в строке
            if (cell) {
                clickOneCell(cell);
            }
        });
    }

    /**
     * Функция обновления данных новым статусом
     * @param oneCell
     */
    function clickOneCell(oneCell)
    {
        let statusValue = 3;

        switch (currentIcon) {
            case IconTurnoutLink:
                statusValue = 0;
                svgData = IconTurnout;
                break;
            case IconNonAppearanceLink:
                statusValue = 1;
                svgData = IconNonAppearance;
                break;
            case IconDistantLink:
                statusValue = 2;
                svgData = IconDistant;
                break;
            case IconDroppedLink:
                statusValue = 3;
                svgData = IconDropped;
                break;
        }

        let oldSVG = oneCell.getElementsByTagName('svg');
        if (oldSVG.length > 0) {
            oldSVG[0].remove();
        }
        oneCell.innerHTML += svgData;

        let statusCell = oneCell.getElementsByClassName('status')[0];
        statusCell.value = statusValue;
    }

    /**
     * Сохранение загруженных svg в переменные
     * @param IconTurnoutLink
     * @param IconNonAppearanceLink
     * @param IconDistantLink
     * @param IconDroppedLink
     * @returns {Promise<void>}
     */
    async function saveSvgFile(IconTurnoutLink, IconNonAppearanceLink, IconDistantLink, IconDroppedLink) {
        IconTurnout = await loadSvgFile(IconTurnoutLink);
        IconNonAppearance = await loadSvgFile(IconNonAppearanceLink);
        IconDistant = await loadSvgFile(IconDistantLink);
        IconDropped = await loadSvgFile(IconDroppedLink);
    }

    /**
     * Загрузка svg
     * @param filePath
     * @returns {Promise<null|string>}
     */
    async function loadSvgFile(filePath) {
        try {
            const response = await fetch(filePath); // Загружаем файл
            if (!response.ok) {
                console.error(response);
            }
            return await response.text();
        } catch (error) {
            console.error(error.message);
            return null;
        }
    }


    document.addEventListener('DOMContentLoaded', function () {
        init();
    });

    /**
     * Функция для изменения иконки и сохранения её состояния
     * @param iconLink
     */
    function changeCursorAndSaveIcon(iconLink) {
        let cursor = 'url('+iconLink+') 0 0, auto';
        if (iconLink === currentIcon) {
            cursor = 'default';
            currentIcon = '';
        } else {
            currentIcon = iconLink;
        }
        document.body.style.cursor = cursor;

        Array.from(elements).forEach(element => {
            element.style.cursor = cursor;
        });
    }

</script>
<style>
    .icon-button {
        text-align: center;
        align-items: center;
    }
    .icon-button svg {
        width: 2em;
        margin-left: 1em;
    }
    .control-label {
        margin-bottom: 1em;
        font-weight: 500;
    }
    .lessons-date:hover {
        border: 1px solid var(--border-color);
    }
    </style>

<div class="journal-edit">

    <?php $form = ActiveForm::begin(); ?>

    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode($this->title) ?>
            </h1>
        </div>
        <div class="flexx space">
            <div class="flexx">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <div class="control-unit">
        <div class="control-label">Выберите статус одной из кнопок расположенных ниже и нажмите на ячейки или столбцы, в которые необходимо установить значение</div>
        <div class="icons-container flexx space-around">
            <!-- Иконки для выбора -->
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconTurnoutLink)">Явка<?= HtmlBuilder::paintSVG(FilePaths::SVG_TURNOUT)?></div>
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconNonAppearanceLink)">Неявка<?= HtmlBuilder::paintSVG(FilePaths::SVG_NON_APPEARANCE)?></div>
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconDistantLink)">Дистант<?= HtmlBuilder::paintSVG(FilePaths::SVG_DISTANT)?></div>
            <div class="icon-button flexx btn-secondary btn" onclick="changeCursorAndSaveIcon(IconDroppedLink)">Нет данных<?= HtmlBuilder::paintSVG(FilePaths::SVG_DROPPED)?></div>
        </div>
    </div>

    <div class="journal-form">

        <?= $form->field($model, 'groupId')->hiddenInput()->label(false) ?>
        <div class="card no-flex">
            <div class="table-topic flexx space">
                <div class="m-auto">
                    Электронный журнал
                </div>
                <div class="flexx">
                    <a class="btn btn-success btn-resize" onclick="resize(100)">+</a>
                    <a class="btn btn-warning btn-resize" onclick="resize(-100)">-</a>
                </div>
            </div>
            <div class="table-block scroll" id="journal">
                <table>
                    <thead id="journal-thead">
                    <tr>
                        <th>ФИО</th>
                        <th colspan="<?= $model->getLessonsCount() ?>">Расписание</th>
                        <th colspan="<?= $model->getColspanControl() ?>">Итоговый контроль</th>
                    </tr>
                    <tr>
                        <td>учащегося</td>
                        <?php foreach ($model->getDateLessons() as $key => $dateLesson) {
                            echo '<td class="lessons-date" onclick="clickOneCellThead(this, '.($key+1).')"> '.$dateLesson.'</td>';
                        }
                        ?>
                        <td style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>">Тема проекта</td>
                        <td style="display: <?= $model->isControlWorkCertificate() ? 'block' : 'none';?>">Оценка</td>
                        <td>Успешное завершение</td>
                    </tr>
                    </thead>

                    <tbody id="journal-tbody">
                    <?php foreach ($model->participantLessons as $participantLesson): ?>
                        <tr>
                            <td>
                                <?= $model->getPrettyParticipant($participantLesson->participant); ?>
                            </td>
                            <?php foreach ($participantLesson->lessonIds as $index => $lesson): ?>
                                <td class="status-participant attendance">
                                    <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]lessonId")
                                        ->hiddenInput(['value' => $lesson->lessonId])
                                        ->label(false) ?>

                                    <?= $form->field($lesson, "[$participantLesson->trainingGroupParticipantId][$index]status")
                                        ->hiddenInput([
                                            'readonly' => true,
                                            'class' => 'status'
                                        ])
                                        /*->dropDownList([
                                            VisitWork::NONE => '---',
                                            VisitWork::ATTENDANCE => 'Я',
                                            VisitWork::NO_ATTENDANCE => 'Н',
                                            VisitWork::DISTANCE => 'Д'
                                        ])*/
                                        ->label(false); ?>
                                </td>
                            <?php endforeach; ?>
                            <td style="display: <?= $model->isProjectCertificate() ? 'block' : 'none';?>">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]groupProjectThemeId")->dropDownList(
                                    ArrayHelper::map($model->availableThemes, 'id', 'projectThemeWork.name'),
                                    ['prompt' => '']
                                )->label(false) ?>
                            </td>
                            <td class="status-participant" style="display: <?= $model->isControlWorkCertificate() ? 'block' : 'none';?>">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]points")->textInput(['type' => 'number'])->label(false) ?>
                            </td>
                            <td class="status-participant">
                                <?= $form->field($participantLesson, "[$participantLesson->trainingGroupParticipantId]successFinishing")->checkbox()->label(false) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <?php ActiveForm::end(); ?>
    <?= HtmlBuilder::upButton();?>
</div>