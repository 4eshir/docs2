<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchPosition */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Должности';
$this->params['breadcrumbs'][] = ['label' => 'Организации / Должности / Люди', 'url' => ['dictionaries/service']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="position-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить должность', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['attribute' => 'name', 'label' => 'Наименование должности'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
