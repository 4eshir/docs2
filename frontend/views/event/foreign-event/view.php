<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\ForeignEventWork */

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
        <?php
        $error = $model->getErrorsWork();
        if ($error !== '' && ((\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7)) || (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6))))
            echo Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы действительно хотите простить все ошибки в карточке учета мероприятия?',
                    'method' => 'post',
                ],]);
        ?>
    </p>

    <div class="content-container" style="color: #ff0000; font: 18px bold;">
        <?php
        $error = $model->getErrorsWork();
        if ($error != '')
        {
            echo '<p style="">';
            echo $error;
            echo '</p>';
        }
        ?>
    </div>

    <?php
    if ($model->business_trip == 0)
    { ?>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'companyString',
                'start_date',
                'finish_date',
                'city',
                'eventWayString',
                'eventLevelString',
                'isMinpros',
                ['attribute' => 'participantsLink', 'format' => 'raw'],
                ['attribute' => 'achievementsLink', 'format' => 'raw'],
                'ageRange',
                'businessTrip',

                ['attribute' => 'orderParticipationString', 'format' => 'raw'],
                ['attribute' => 'addOrderParticipationString', 'format' => 'raw'],

                'key_words',
                ['attribute' => 'docString', 'format' => 'raw'],
                'creatorString',
                'editorString',
            ],
        ]) ?>
    <?php }
    else
    { ?>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'companyString',
                'start_date',
                'finish_date',
                'city',
                'eventWayString',
                'eventLevelString',
                ['attribute' => 'participantsLink', 'format' => 'raw'],
                ['attribute' => 'achievementsLink', 'format' => 'raw'],
                'ageRange',
                'businessTrip',
                ['attribute' => 'escort_id', 'value' => function ($model) { return $model->escort->shortName; }],
                ['attribute' => 'orderBusinessTripString', 'format' => 'raw'],

                ['attribute' => 'orderParticipationString', 'format' => 'raw'],
                ['attribute' => 'addOrderParticipationString', 'format' => 'raw'],

                'key_words',
                ['attribute' => 'docString', 'format' => 'raw'],
            ],
        ]) ?>
    <?php
    }
    ?>


</div>
