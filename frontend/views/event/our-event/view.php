<?php

use common\helpers\files\FilesHelper;
use frontend\models\work\event\EventWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model EventWork */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить это мероприятие?',
                'method' => 'post',
            ],
        ]) ?>
        <?php
        /*$error = $model->getErrorsWork();
        if ($error !== '' && ((\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7)) || (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6))))
            echo Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы действительно хотите простить все ошибки в мероприятии?',
                    'method' => 'post',
                ],]);*/
        ?>
    </p>

    <div class="content-container" style="color: #ff0000; font: 18px bold;">
        <?php
        /*$error = $model->getErrorsWork();
        if ($error != '')
        {
            echo '<p style="">';
            echo $error;
            echo '</p>';
        }*/
        ?>
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
