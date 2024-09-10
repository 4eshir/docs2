<?php

use frontend\models\work\dictionaries\CompanyWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model CompanyWork */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="company-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную организацию?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Тип организации', 'attribute' => 'company_type', 'value' => function($model){
                return Yii::$app->companyType->get($model->company_type);
            }],
            ['label' => 'Название организации', 'attribute' => 'name'],
            ['label' => 'Краткое название', 'attribute' => 'short_name'],
            'editString',
        ],
    ]) ?>

    <?php
    if ($model->is_contractor)
    {
        echo '<h4><u>Информация по контрагенту</u></h4>';

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['label' => 'ИНН организации', 'attribute' => 'inn'],
                ['label' => 'Категория СМСП', 'attribute' => 'categorySmspString'],
                'ownershipTypeString',
                ['label' => 'Комментарий', 'attribute' => 'comment'],
                'phone_number',
                'email',
                'okved',
                'head_fio',
                'site',
            ],
        ]);

        echo '<h4><u>Связанные договора</u></h4>';

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['label' => 'Договора', 'attribute' => 'contractArray', 'format' => 'raw'],
            ],
        ]);
    }
    ?>

</div>
