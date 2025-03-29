<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchLocalResponsibility */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Учет ответственности работников';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="local-responsibility-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить ответственность', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php

    $gridColumns = [
        ['attribute' => 'responsibilityTypeStrEx', 'label' => 'Вид ответственности'],
        ['attribute' => 'branchStrEx', 'label' => 'Отдел'],
        ['attribute' => 'auditoriumStrEx' ,'label' => 'Помещение'],
        ['attribute' => 'quantEx', 'label' => 'Квант'],
        ['attribute' => 'peopleStrEx', 'label' => 'Работник'],
        ['attribute' => 'orderStrEx', 'label' => 'Приказ'],
        ['attribute' => 'regulationStrEx', 'label' => 'Положение/инструкция'],

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

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
        'columns' => [

            ['attribute' => 'responsibilityTypeStr', 'format' => 'raw', 'label' => 'Вид ответственности'],
            ['attribute' => 'branchStr', 'format' => 'raw', 'label' => 'Отдел'],
            ['attribute' => 'auditoriumStr', 'format' => 'raw', 'label' => 'Помещение'],
            ['attribute' => 'quant', 'format' => 'raw', 'label' => 'Квант'],
            ['attribute' => 'peopleStr', 'format' => 'raw', 'label' => 'Работник'],
            ['attribute' => 'orderStr', 'format' => 'raw', 'label' => 'Приказ'],
            ['attribute' => 'regulationStr', 'format' => 'raw', 'label' => 'Положение/инструкция'],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
