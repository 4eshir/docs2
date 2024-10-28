<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */

$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
if ($model->quant !== null)
    $this->title .= ' №' . $model->quant;
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="local-responsibility-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную ответственность?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h4><u>Общая информация</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'responsibilityTypeStr', 'format' => 'raw'],
            ['attribute' => 'branchStr', 'format' => 'raw'],
            ['attribute' => 'auditoriumStr', 'format' => 'raw'],
            ['attribute' => 'quant', 'format' => 'raw'],
            ['attribute' => 'peopleStr', 'format' => 'raw'],
            ['attribute' => 'orderStr', 'format' => 'raw', 'label' => 'Приказ'],
            ['attribute' => 'regulationStr', 'format' => 'raw'],
            ['label' => 'Файлы', 'attribute' => 'files', 'value' => function ($model) {
                $split = explode(" ", $model->files);
                $result = '';
                for ($i = 0; $i < count($split) - 1; $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['local-responsibility/get-file', 'fileName' => $split[$i]])).'<br>';
                return $result;
            }, 'format' => 'raw'],
        ],
    ]) ?>
    <br>
    <h4><u>История ответственности</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'legacyResp', 'label' => 'История', 'format' => 'raw'],
        ],
    ]) ?>

</div>
