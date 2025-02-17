<?php

use common\helpers\files\FilesHelper;
use frontend\models\work\event\EventWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model EventWork */
/* @var $buttonsAct */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">

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
                        Период проведения
                    </div>
                    <div class="field-date">
                        <?= $model->getDatePeriod(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Адрес
                    </div>
                    <div class="field-date">
                        <?= $model->getAddress(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тип
                    </div>
                    <div class="field-date">
                        <?= $model->getEventType(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Форма
                    </div>
                    <div class="field-date">
                        <?= $model->getEventForm(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Уровень
                    </div>
                    <div class="field-date">
                        <?= $model->getEventLevel(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Формат проведения
                    </div>
                    <div class="field-date">
                        <?= $model->getEventWay(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Информация об участниках</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Общее кол-во детей:
                    </div>
                    <div class="field-date">
                        <?= $model->getChildParticipantsCount(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во детей от РШТ:
                    </div>
                    <div class="field-date">
                        <?= $model->getChildRSTParticipantsCount(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Возрастные ограничения:
                    </div>
                    <div class="field-date">
                        <?= $model->getAgeRestrictions(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во преподавателей:
                    </div>
                    <div class="field-date">
                        <?= $model->getTeacherParticipantsCount(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кол-во других участников:
                    </div>
                    <div class="field-date">
                        <?= $model->getOtherParticipantsCount(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?= ''/*$model->getFullScan();*/ ?><div>Протокол мероприятия</div></div>
                    <div class="file-block-center"><?= ''/*$model->getFullScan();*/ ?><div>Фотоматериалы</div></div>
                    <div class="file-block-center"><?= ''/*$model->getFullScan();*/ ?><div>Явочные документы</div></div>
                    <div class="file-block-center"><?= ''/*$model->getFullScan();*/ ?><div>Другие файлы</div></div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Дополнительная информация</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Сферы участия
                    </div>
                    <div class="field-date">
                        <?= $model->getScopesString(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Мероприятие проводит
                    </div>
                    <div class="field-date">
                        <?= $model->getEventBranches(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Ответственный(-ые) работник(-и)
                    </div>
                    <div class="field-date">
                        <?= $model->getResponsibles(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Содержит образовательные программы
                    </div>
                    <div class="field-date">
                        <?= $model->getContainsEducation(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Примечание
                    </div>
                    <div class="field-date">
                        <?= $model->getComment(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Связанные документы</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Приказ
                    </div>
                    <div class="field-date">
                        <?= $model->getOrderNameRaw(); ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Положение
                    </div>
                    <div class="field-date">
                        <?= $model->getRegulationRaw(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Связанные группы</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= $model->getDatePeriod(); ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
                <div class="card-field flexx">
                    <div class="field-date">
                        <?= $model->getDatePeriod(); ?>
                    </div>
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
                            <?= ''/*$model->getCreatorName();*/ ?>
                        </div>
                    </div>
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Последний редактор
                        </div>
                        <div class="field-date">
                            <?= ''/*$model->getLastEditorName();*/ ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'start_date',
            'finish_date',
            ['attribute' => 'event_type', 'value' => function(EventWork $model){
                return Yii::$app->eventType->get($model->event_type);
            }],
            ['attribute' => 'event_form', 'value' => function(EventWork $model){
                return Yii::$app->eventForm->get($model->event_form);
            }],
            ['attribute' => 'event_way', 'value' => function(EventWork $model){
                return Yii::$app->eventWay->get($model->event_way);
            }],
            'address',
            ['attribute' => 'event_level', 'value' => function(EventWork $model){
                return Yii::$app->eventLevel->get($model->event_level);
            }],
            ['attribute' => 'scopesString', 'format' => 'raw'],
            ['attribute' => 'participants_count', 'value' => function (EventWork $model){
                return $model->child_participants_count + $model->teacher_participants_count + $model->other_participants_count;
            }],
            'child_participants_count',
            'child_rst_participants_count',
            'teacher_participants_count',
            'other_participants_count',
            'leftAge',
            'rightAge',
            ['attribute' => 'is_federal', 'value' => function($model){
                if ($model->is_federal == 1) {
                    return 'Да';
                }
                else {
                    return 'Нет';
                }
            }],
            ['attribute' => 'responsibles', 'format' => 'raw'],
            ['attribute' => 'eventBranches', 'label' => 'Мероприятие проводит', 'format' => 'raw'],
            ['attribute' => 'contains_education', 'value' => function($model){
                if ($model->contains_education == 0)
                    return 'Не содержит образовательных программы';
                else
                    return 'Содержит образовательные программы';
            }],
            'key_words',
            'comment',
            ['attribute' => 'order_id', 'value' => function (EventWork $model) {
                return 'Coming soon';
            }, 'format' => 'raw'],
            ['attribute' => 'regulationRaw', 'label' => 'Положение', 'format' => 'raw'],
            ['label' => 'Протоколы мероприятия', 'attribute' => 'protocol', 'value' => function (EventWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PROTOCOL), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Фотоматериалы', 'attribute' => 'photoFiles', 'value' => function ($model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_PHOTO), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Явочные документы', 'attribute' => 'reporting_doc', 'value' => function ($model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_REPORT), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Другие файлы', 'attribute' => 'otherFiles', 'value' => function ($model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_OTHER), 'link'));
            }, 'format' => 'raw'],
            ['attribute' => 'linkGroups', 'format' => 'raw'],
            ['label' => 'Создатель карточки', 'attribute' => 'creatorString', 'value' => function (EventWork $model) {
                return $model->creatorWork ? $model->creatorWork->getFullName() : '';
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>
