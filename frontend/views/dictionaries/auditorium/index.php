<?php

use frontend\models\search\SearchAuditorium;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel SearchAuditorium */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Помещения';
$this->params['breadcrumbs'][] = ['label' => 'Отделы / Помещения / Виды ответственности', 'url' => ['dictionaries/premises']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditorium-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить помещение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        ['attribute' => 'name', 'label' => 'Уникальный глобальный номер'],
        ['attribute' => 'text', 'label' => 'Имя'],
        ['attribute' => 'square', 'label' => 'Площадь (кв.м)'],
        ['attribute' => 'isEducation', 'label' => 'Предназначен для обр. деят.'],
        ['attribute' => 'branch_id', 'label' => 'Название отдела', 'value' => function($model){
                    return $model->branch->name;}],
        ['attribute' => 'capacity', 'label' => 'Кол-во ученико-мест'],
        ['attribute' => 'auditoriumTypeString', 'label' => 'Тип помещения'],
        ['attribute' => 'window_count', 'label' => 'Кол-во оконных проемов'],
        ['attribute' => 'includeSquareStr', 'label' => 'Учитывается при подсчете площади'],
    ];
    echo '<b>Скачать файл </b>';
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,

        'options' => [
            'padding-bottom: 100px',
        ]
    ]);

    ?>
    <div style="margin-bottom: 10px">

    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'name',
            'text',
            'square',
            ['attribute' => 'auditoriumTypeString', 'label' => 'Тип помещения'],
            ['attribute' => 'isEducation', 'label' => 'Предназначен для обр. деят.'],
            ['attribute' => 'branchName', 'label' => 'Название отдела', 'format' => 'html'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
