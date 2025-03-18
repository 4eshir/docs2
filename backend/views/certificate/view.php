<?php

use backend\forms\CertificateTemplatesForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model CertificateTemplatesForm */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны сертификатов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="certificat-templates-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->entity->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->entity->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить шаблон сертификата?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'entity.name',
            [
                'attribute' => 'templateFile',
                'format' => 'raw',
                'value' => function (CertificateTemplatesForm $model) {
                    $imageUrl = Url::to(['certificate/get-image', 'filepath' => $model->entity->path]);
                    return Html::img($imageUrl, ['alt' => 'Изображение', 'style' => 'max-width:70%;']);
                },
            ],
        ],
    ]) ?>

</div>