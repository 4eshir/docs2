<?php

use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\general\PeopleWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $groupsList string */
/* @var $studentAchievements string */
/* @var $responsibilities string */
/* @var $positions string */

$this->title = $model->getFIO(PersonInterface::FIO_FULL);
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="people-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этого человека?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h4><u>Общая информация</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Фамилия', 'attribute' => 'surname'],
            ['label' => 'Имя', 'attribute' => 'firstname'],
            ['label' => 'Отчество', 'attribute' => 'patronymic'],
            ['label' => 'Организации и должности', 'value' => $positions, 'format' => 'raw'],
            ['label' => 'Отдел по трудовому договору', 'attribute' => 'branch', 'format' => 'raw', 'value' => function($model) {
                return Html::a(Yii::$app->branches->get($model->branch), Url::to(['branch/view', 'id' => $model->branch]));
            }, 'visible' => $model->branch !== null],

            ['label' => 'Уникальный идентификатор', 'attribute' => 'short', 'format' => 'raw', 'visible' => $model->short !== null && $model->short !== ''],
            ['label' => 'Дата рождения', 'attribute' => 'birthdate', 'visible' => $model->birthdate !== null && $model->birthdate !== ''],
            ['label' => 'Пол', 'attribute' => 'sexString', 'visible' => $model->sex !== null && $model->sex !== ''],
        ],
    ]) ?>

    <h4><u>Информация об образовательной деятельности</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Группы', 'attribute' => 'groupsList', 'format' => 'raw', 'value' => $groupsList],
            ['label' => 'Достижения учеников', 'attribute' => 'studentAchievements', 'format' => 'raw', 'value' => $studentAchievements],
        ],
    ]) ?>

    <h4><u>Ответственность работника</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Ответственности', 'attribute' => 'responsibilities', 'format' => 'raw', 'value' => $responsibilities],
        ],
    ]) ?>

</div>
