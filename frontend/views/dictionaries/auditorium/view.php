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
            ['attribute' => 'isEducation', 'label' => 'Предназначен для обр. деят.'],
            ['attribute' => 'capacity', 'visible' => $model->is_education === 1],
            ['attribute' => 'auditoriumTypeString', 'visible' => $model->is_education === 1],
            ['attribute' => 'branchName', 'label' => 'Название отдела', 'format' => 'html'],
            ['attribute' => 'isIncludeSquare', 'label' => 'Учитывается при подсчете общей площади'],
            'window_count',
            ['attribute' => 'filesList', 'value' => function (AuditoriumWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_OTHER), 'link'));
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>
