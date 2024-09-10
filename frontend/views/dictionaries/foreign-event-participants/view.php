<?php

use common\helpers\DateFormatter;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ForeignEventParticipantsWork */

$this->title = $model->getFIO(ForeignEventParticipantsWork::FIO_FULL);
$this->params['breadcrumbs'][] = ['label' => 'Участники деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>

    .badge {
        padding: 3px 9px 4px;
        font-size: 13px;
        font-weight: bold;
        white-space: nowrap;
        color: #ffffff;
        background-color: #999999;
        -webkit-border-radius: 9px;
        -moz-border-radius: 9px;
        border-radius: 9px;
    }
    .badge:hover {
        color: #ffffff;
        text-decoration: none;
        cursor: pointer;
    }
    .badge-error {
        background-color: #b94a48;
    }
    .badge-error:hover {
        background-color: #953b39;
    }
    .badge-success {
        background-color: #468847;
    }
    .badge-success:hover {
        background-color: #356635;
    }

</style>

<div class="foreign-event-participants-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить участника?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <h4><u>Общая информация</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'firstname',
            'surname',
            'patronymic',
            ['label' => 'Дата рождения', 'attribute' => 'birthdate', 'value' => function(ForeignEventParticipantsWork $model) {
                return DateFormatter::format($model->birthdate, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }],
            ['attribute' => 'sex', 'value' => function(ForeignEventParticipantsWork $model) {
                return $model->getSexString();
            }],
            'email',
        ],
    ]) ?>

    <h4><u>Информация об участии в мероприятиях</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'documents', 'format' => 'raw'],
            ['attribute' => 'achievements', 'format' => 'raw'],
            ['attribute' => 'participantEvents', 'format' => 'raw'],
        ],
    ]) ?>

    <h4><u>Информация об участии в образовательных программах</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'studies', 'format' => 'raw'],
        ],
    ]) ?>

    <h4><u>Разглашение персональных данных</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'personalData', 'value' => function(ForeignEventParticipantsWork $model) {
                return $model->createRawPersonalData();
            },
            'format' => 'html', 'label' => false],
        ],
    ]) ?>

</div>
