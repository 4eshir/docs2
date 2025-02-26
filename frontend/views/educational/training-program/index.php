<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\SearchTrainingProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

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

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>

                <div class="export-menu">
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
                    echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,

                        'options' => [
                            'padding-bottom: 100px',
                        ],
                    ]);

                    ?>
                </div>
            </div>

            <?= HtmlCreator::filterToggle() ?>
        </div>
    </div>

    <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 10px">

    <?= GridView::widget([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            /*['class' => 'yii\grid\CheckboxColumn', 'header' => 'Акт.',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $options['checked'] = (bool)$model->actual;
                    $options['class'] = 'check';
                    return $options;
                }],*/
            ['attribute' => 'name'],
            ['attribute' => 'levelNumber', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'branchString', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'pedCouncilDate', 'encodeLabel' => false, 'label' => 'Дата<br>пед. сов.'],
            ['attribute' => 'authorString', 'format' => 'html'],
            ['attribute' => 'capacity'],
            ['attribute' => 'agePeriod', 'encodeLabel' => false],
            ['attribute' => 'focusString'],
            ['attribute' => 'allowRemote', 'encodeLabel' => false, 'label' => 'Форма<br>реализации'],

            ['class' => VerticalActionColumn::class],
        ],
        'rowOptions' => function ($model) {
            return ['data-href' => Url::to([Yii::$app->frontUrls::PROGRAM_VIEW, 'id' => $model->id])];
        },
    ]); ?>

    <div class="form-group">
        <?php echo Html::button('Сохранить статус программ', ['class' => 'btn btn-success', 'onclick' => 'archive()']) ?>
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

    <?php
    $this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
    ?>
