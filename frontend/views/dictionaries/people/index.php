<?php

use frontend\models\search\SearchPeople;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SearchPeople */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Люди';
$this->params['breadcrumbs'][] = ['label' => 'Организации / Должности / Люди', 'url' => ['dictionaries/service']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить человека', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['attribute' => 'surname', 'label' => 'Фамилия'],
            ['attribute' => 'firstname', 'label' => 'Имя'],
            ['attribute' => 'patronymic', 'label' => 'Отчество'],
            ['attribute' => 'positionsWork', 'label' => 'Должности', 'format' => 'raw'],
            /*['attribute' => 'positionsWork', 'label' => 'Должность', 'format' => 'raw'],*/ // вот это работает, но нужно подшаманить с фильтрами
            ['attribute' => 'companyName', 'label' => 'Организация', 'value' => function($model){
                return $model->company->name;
            }],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
