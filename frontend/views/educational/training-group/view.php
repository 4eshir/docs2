<?php

use common\helpers\StringFormatter;
use frontend\forms\training_group\TrainingGroupCombinedForm;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\general\PeopleWork;
use frontend\services\educational\JournalService;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrainingGroupCombinedForm */
/* @var $journalState */
/* @var $buttonsAct */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Группа '.$this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="training-group-view">

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
                        Отдел
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Педагоги
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Период обучения
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Программа и форма обучения</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Образ. программа
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма обучения
                    </div>
                    <div class="field-date">
                        <?= 'Бюджет сетвевая' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Приказы</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Загрузка приказов
                    </div>
                    <div class="field-date">
                        <?= 'разрешена/запрещена' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Документы
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Дополнительная информация</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Выработка чел/ч
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во детей
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во занятий
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Учебный график и состав</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Расписание
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Состав группы
                    </div>
                    <div class="field-date">
                        <?= '' ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Итоговый контроль
                    </div>
                    <div class="field-date">
                        <?= '' ?>
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
                            <?= '$model->getCreatorName();' ?>
                        </div>
                    </div>
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Последний редактор
                        </div>
                        <div class="field-date">
                            <?= '$model->getLastEditorName();' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p>
        <?= Html::a('Перенести темы занятий из ОП', ['create-lesson-themes', 'groupId' => $model->id], ['class' => 'btn btn-secondary']) ?>
        <?= Html::a('Редактировать', ['base-form', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if ($journalState == JournalService::JOURNAL_EMPTY): ?>
            <?= Html::a('Создать журнал', ['generate-journal', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php elseif ($journalState == JournalService::JOURNAL_EXIST): ?>
            <?= Html::a('Открыть журнал', ['educational/journal/view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить журнал', ['delete-journal', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
        <?php endif; ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить группу?',
                'method' => 'post',
            ],
        ]) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'branch', 'label' => 'Отдел производящий учет', 'format' => 'html', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->branch ? Yii::$app->branches->get($model->branch) : '';
            }],
            ['attribute' => 'number', 'label' => 'Номер группы'],
            ['attribute' => 'budget', 'label' => 'Форма обучения', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->budget == 1 ? 'Бюджет' : 'Внебюджет';
            }],
            ['attribute' => 'trainingProgram', 'format' => 'html', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->trainingProgram ?
                    StringFormatter::stringAsLink(
                        $model->trainingProgram->name,
                        Url::to(['educational/training-program/view', 'id' => $model->trainingProgram->id])
                    ) : '';
            }],
            ['attribute' => 'network', 'label' => 'Сетевая форма обучения', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->network == 1 ? 'Да' : 'Нет';
            }],
            ['attribute' => 'teachersList', 'format' => 'html', 'value' => function (TrainingGroupCombinedForm $model){
                $newTeachers = [];
                foreach ($model->teachers as $teacher) {
                    /** @var TeacherGroupWork $teacher */
                    $newTeachers[] = StringFormatter::stringAsLink(
                            $teacher->teacherWork->getFIO(PersonInterface::FIO_FULL),
                            Url::to(['dictionaries/people/view', 'id' => $teacher->teacherWork->people_id])
                    );
                }
                return implode('<br>', $newTeachers);
            }],
            ['attribute' => 'startDate', 'label' => 'Дата начала занятий'],
            ['attribute' => 'endDate', 'label' => 'Дата окончания занятий'],
            ['attribute' => 'photoFiles', 'value' => function (TrainingGroupCombinedForm $model) {
                return $model->photoFiles;
            }, 'format' => 'raw'],
            ['attribute' => 'presentationFiles', 'value' => function (TrainingGroupCombinedForm $model) {
                return $model->presentationFiles;
            }, 'format' => 'raw'],
            ['attribute' => 'workFiles', 'value' => function (TrainingGroupCombinedForm $model) {
                return $model->workMaterialFiles;
            }, 'format' => 'raw'],
            /*
            ['attribute' => 'countParticipants', 'label' => 'Количество учеников', 'format' => 'html'],
            ['attribute' => 'participantNames', 'value' => '<button class="accordion">Показать состав группы</button><div class="panel">'.$model->participantNames.'</div>', 'format' => 'raw'],
            ['attribute' => 'countLessons', 'label' => 'Количество занятий в расписании', 'format' => 'html'],
            ['attribute' => 'lessonDates', 'value' => '<button class="accordion">Показать расписание группы</button><div class="panel">'.$model->lessonDates.'</div>', 'format' => 'raw'],
            ['attribute' => 'manHoursPercent', 'format' => 'raw', 'label' => 'Выработка человеко-часов'],
            ['attribute' => 'journalLink', 'format' => 'raw', 'label' => 'Журнал'],
            ['attribute' => 'ordersName', 'format' => 'raw'],
            */

            //['attribute' => 'openText', 'label' => 'Темы занятий перенесены (при наличии)'],
        ],
    ]) ?>

    <h4><u>Ученики</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'participants', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyParticipants());
            }],
        ],
    ]) ?>

    <h4><u>Занятия</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'lessons', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyLessons());
            }],
        ],
    ]) ?>

    <h4><u>Сведения о защите работ</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'protection_date'],
            ['attribute' => 'themes', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyThemes());
            }],
            ['attribute' => 'experts', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyExperts());
            }],
        ],
    ]) ?>

</div>
