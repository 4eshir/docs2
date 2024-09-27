<?php

use frontend\models\search\SearchCompany;
use frontend\models\work\dictionaries\CompanyWork;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SearchCompany */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Организации';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить организацию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            ['attribute' => 'inn', 'label' => 'ИНН'],
            ['attribute' => 'name', 'label' => 'Наименование'],
            ['attribute' => 'short_name', 'label' => 'Краткое наименование'],
            ['attribute' => 'company_type', 'label' => 'Тип организации', 'value' => function(CompanyWork $model){
                return Yii::$app->companyType->get($model->company_type);
            }],
            'contractorString',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
