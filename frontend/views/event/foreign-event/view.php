<?php

use common\helpers\files\FilesHelper;
use frontend\forms\event\ForeignEventForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ForeignEventForm */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="foreign-event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить мероприятие?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы действительно хотите простить все ошибки в карточке учета мероприятия?',
                    'method' => 'post',
                ],]);
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'name', 'value' => function (ForeignEventForm $model) {
                return $model->name;
            }],
            ['attribute' => 'organizer'],
            ['attribute' => 'startDate', 'value' => function (ForeignEventForm $model) {
                return $model->startDate;
            }],
            ['attribute' => 'endDate'],
            ['attribute' => 'city'],
            ['attribute' => 'eventWay', 'value' => function (ForeignEventForm $model) {
                return Yii::$app->eventWay->get($model->format);
            }],
            ['attribute' => 'eventLevel', 'value' => function (ForeignEventForm $model) {
                return Yii::$app->eventLevel->get($model->level);
            }],
            ['attribute' => 'minister', 'value' => function (ForeignEventForm $model) {
                return Yii::$app->eventWay->get($model->format);
            }],
            ['attribute' => 'participantsLink', 'format' => 'raw', 'value' => function (ForeignEventForm $model) {
                return $model->getParticipantsLink();
            }],
            ['attribute' => 'achievementsLink', 'format' => 'raw', 'value' => function (ForeignEventForm $model) {
                return $model->getAchievementsLink();
            }],
            ['attribute' => 'achievementsLink', 'format' => 'raw', 'value' => function (ForeignEventForm $model) {
                return $model->getAgeRange();
            }],
            ['attribute' => 'businessTrip', 'format' => 'raw', 'value' => function (ForeignEventForm $model) {
                return !is_null($model->orderBusinessTrip) ? 'Есть' : 'Нет';
            }],

            ['attribute' => 'orderParticipationString', 'format' => 'raw', 'value' => function (ForeignEventForm $model) {
                return $model->getOrderParticipant();
            }],
            ['attribute' => 'addOrderParticipationString', 'format' => 'raw', 'value' => function (ForeignEventForm $model) {
                return $model->getAddOrderParticipant();
            }],

            ['attribute' => 'keyWords'],
            ['attribute' => 'doc', 'format' => 'raw', 'value' => function (ForeignEventForm $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_DOC), 'link'));
            }],
            'creatorString',
            'editorString',
        ],
    ]) ?>

</div>
