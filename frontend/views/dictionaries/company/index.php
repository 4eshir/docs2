<?php

use common\models\search\SearchCompany;
use common\models\work\general\CompanyWork;
use yii\helpers\Html;
use yii\grid\GridView;

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
