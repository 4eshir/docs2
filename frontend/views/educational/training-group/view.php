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
<?= \common\helpers\html\HtmlBuilder::createTooltip('В своём стремлении улучшить пользовательский опыт мы упускаем'); ?>
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
                        <?= $model->getBranch(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Педагоги
                    </div>
                    <div class="field-date">
                        <?= $model->getTeachersRaw(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Период обучения
                    </div>
                    <div class="field-date">
                        <?= $model->getTrainingPeriod(); ?>
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
                        <?= $model->getTrainingProgramRaw(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма обучения
                    </div>
                    <div class="field-date">
                        <?= $model->getFormStudy(); ?>
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
                        <?= $model->getConsentOrders(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Документы
                    </div>
                    <div class="field-date">
                        <?= $model->getOrdersRaw(); ?>
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
                        <?= $model->getPrettyManHoursPercent(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во детей
                    </div>
                    <div class="field-date">
                        <?= $model->getCountParticipants(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во занятий
                    </div>
                    <div class="field-date">
                        <?= $model->getCountLessons(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Допущена к итоговому контролю
                    </div>
                    <div class="field-date">
                        <?= $model->getProtectionConfirm(); ?>
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
                        <?= $model->getPrettyLessons() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Состав группы
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyParticipants() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Итоговый контроль
                    </div>
                    <div class="field-date">
                        <?= $model->getPrettyFinalControl(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= $model->getFullPhotoFiles(); ?><div>Фотоматериалы</div></div>
                    <div class="file-block-center"><?= $model->getFullPresentationFiles(); ?><div>Презентационные материалы</div></div>
                    <div class="file-block-center"><?= $model->getFullWorkFiles(); ?><div>Рабочие материалы</div></div>
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

</div>
