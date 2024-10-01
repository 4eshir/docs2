<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\SearchTrainingProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Образовательные программы';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    function archive() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        //var p = getUrlParameter('page');
        //if (p == false) p = 1;
        var checkboxes = document.getElementsByClassName('check');
        var archive = [];
        var unarchive = [];
        for (var index = 0; index < checkboxes.length; index++) {
            if (checkboxes[index].checked)
                archive.push(checkboxes[index].value);
            else
                unarchive.push(checkboxes[index].value);
        }
        window.location.href='<?php echo Url::to(['training-program/archive']); ?>&arch='+archive.join()+'&unarch='+unarchive.join();
        //$('#grid').yiiGridView('getSelectedRows')
    }
</script>

<div class="training-program-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить программу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div style="margin: 0 118%;">
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Зеленый цвет - образовательная программа актуальная и не имеет ошибок&#10Желтый цвет - образовательная программа имеет ошибку&#10Белый цвет - образовательная программа не актуальная и не имеет ошибок">❔</div>
    </div>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        'actualExport',
        'name',
        ['attribute' => 'level', 'label' => 'Ур. сложности','value' => function ($model) {return $model->level+1;}],
        ['attribute' => 'branchs', 'label' => 'Место реализации', 'format' => 'html'],
        ['attribute' => 'ped_council_date', 'label' => 'Дата пед. сов.'],
        ['attribute' => 'ped_council_number', 'label' => '№ пед. сов.'],
        ['attribute' => 'compilers', 'format' => 'html'],
        'capacity',
        'studentAge',
        'stringFocus',
        ['attribute' => 'allowRemote', 'label' => 'Форма реализации'],

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

    <?php

    echo GridView::widget([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Акт.',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $options['checked'] = (bool)$model->actual;
                    $options['class'] = 'check';
                    return $options;
                }],
            'name',//'nameX',
            ['attribute' => 'level', 'label' => 'Ур. сложности','value' => function ($model) {return $model->level+1;}],
            ['attribute' => 'branchs', 'label' => 'Место реализации', 'format' => 'html'],
            ['attribute' => 'ped_council_date', 'label' => 'Дата пед. сов.'],
            ['attribute' => 'ped_council_number', 'label' => '№ пед. сов.'],
            ['attribute' => 'compilers', 'format' => 'html'],
            'capacity',
            'studentAge',
            'stringFocus',
            ['attribute' => 'allowRemote', 'label' => 'Форма реализации'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <div class="form-group">
        <!--<a class="btn btn-danger" href="/index.php?r=training-group%2Findex&archive=">Сохранить архив</a>-->
        <?php echo Html::button('Сохранить статус программ', ['class' => 'btn btn-success', 'onclick' => 'archive()']) ?>
        <?php //echo Html::submitButton('Сохранить архив', ['class' => 'btn btn-success']) ?>
    </div>

</div>

    <?php
    $url = Url::toRoute(['training-program/actual']);
    $this->registerJs(
    "function myStatus(id){
        $.ajax({
            type: 'GET',
            url: 'index.php?r=training-program/actual',
            data: {id: id},
            success: function(result){
                console.log(result);
            }
        });
    }", yii\web\View::POS_END);
    ?>