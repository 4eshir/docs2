<?php

use common\helpers\files\FilesHelper;
use frontend\models\work\dictionaries\AuditoriumWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model AuditoriumWork */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Помещения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auditorium-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить помещение?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'name',
            'square',
            'text',
            ['attribute' => 'isEducation', 'label' => 'Предназначен для обр. деят.', 'value' => function (AuditoriumWork $model) {
                return $model->getEducationPretty();
            }],
            ['attribute' => 'capacity', 'visible' => $model->isEducation()],
            ['attribute' => 'auditoriumTypeString', 'visible' => $model->isEducation(), 'value' => function (AuditoriumWork $model) {
                return $model->getAuditoriumTypePretty();
            }],
            ['attribute' => 'branch', 'label' => 'Название отдела', 'format' => 'html', 'value' => function (AuditoriumWork $model) {
                return Yii::$app->branches->get($model->branch);
            }],
            ['attribute' => 'isIncludeSquare', 'label' => 'Учитывается при подсчете общей площади', 'value' => function (AuditoriumWork $model) {
                return $model->getIncludeSquarePretty();
            }],
            'window_count',
            ['attribute' => 'filesList', 'value' => function (AuditoriumWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_OTHER), 'link'));
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>
