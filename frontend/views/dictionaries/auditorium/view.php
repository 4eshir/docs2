<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\AuditoriumWork */

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
            ['attribute' => 'files', 'value' => function ($model) {
                $split = explode(" ", $model->files);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['auditorium/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'files'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>
