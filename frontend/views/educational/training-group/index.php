<?php

use frontend\models\search\SearchTrainingGroup;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel SearchTrainingGroup */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Учебные группы';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

    function archive() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        var p = getUrlParameter('page');
        if (p == false) p = 1;
        var checkboxes = document.getElementsByClassName('check');
        var archive = [];
        var unarchive = [];
        for (var index = 0; index < checkboxes.length; index++) {
            if (checkboxes[index].checked)
                archive.push(checkboxes[index].value);
            else
                unarchive.push(checkboxes[index].value);
        }
        window.location.href='<?php echo Url::to(['training-group/archive']); ?>&arch='+archive.join()+'&unarch='+unarchive.join();
        //$('#grid').yiiGridView('getSelectedRows')
    }
</script>



<div class="training-group-index">


    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить новую учебную группу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div style="margin: 0 103%;">
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Белый цвет - группа не имеет ошибок&#10Желтый цвет - у группы имеются ошибки&#10Красный цвет - у группы имеется критическая ошибка&#10Серый цвет - группа находится в архиве">❔</div>
    </div>

    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <div style="margin-bottom: 10px;">
        <?php

        $gridColumns = [
            ['attribute' => 'numberView', 'format' => 'html'],
                ['attribute' => 'programName', 'format' => 'html'],
                ['attribute' => 'branchName', 'label' => 'Отдел', 'format' => 'raw'],
                ['attribute' => 'pureCountParticipants', 'label' => 'Кол-во детей'],
                ['attribute' => 'teachersList', 'format' => 'html'],
                'start_date',
                'finish_date',
                ['attribute' => 'budgetText', 'label' => 'Бюджет', 'filter' => [ 1 => "Бюджет", 0 => "Внебюджет"]],

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
    </div>

    <?php echo GridView::widget([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Архив',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    //$options['onclick'] = 'myStatus('.$model->id.');';
                    $options['checked'] = $model->archive ? true : false;
                    $options['class'] = 'check';
                    return $options;
                }],
            ['attribute' => 'numberView', 'format' => 'html'],
            ['attribute' => 'programName', 'format' => 'html'],
            ['attribute' => 'branchName', 'label' => 'Отдел', 'format' => 'raw'],
            ['attribute' => 'teachersList', 'format' => 'html'],
            'start_date',
            'finish_date',
            ['attribute' => 'budgetText', 'label' => 'Бюджет', 'filter' => [ 1 => "Бюджет", 0 => "Внебюджет"]],

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action == 'update') {
                        return Url::toRoute(['educational/training-group/' . 'base-form', 'id' => $model->id]);
                    }
                    return Url::toRoute(['educational/training-group/' . $action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <div class="form-group">
        <!--<a class="btn btn-danger" href="/index.php?r=training-group%2Findex&archive=">Сохранить архив</a>-->
        <?php echo Html::button('Сохранить архив', ['class' => 'btn btn-success', 'onclick' => 'archive()']) ?>
        <?php //echo Html::submitButton('Сохранить архив', ['class' => 'btn btn-success']) ?>
    </div>

</div>

<?php
$url = Url::toRoute(['training-group/archive']);
$this->registerJs(
    "function myStatus(id){
        $.ajax({
            type: 'GET',
            url: 'index.php?r=training-group/archive',
            data: {id: id},
            success: function(result){
                console.log(result);
            }
        });
    }", yii\web\View::POS_END);
?>