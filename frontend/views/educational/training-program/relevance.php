<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\SearchTrainingProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Образовательные программы';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="training-program-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
        </div>
    </div>

    <?= $this->render('_search-relevance', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 10px">
<?php
var_dump(Yii::$app->request->queryParams['r']);
?>
    <?= GridView::widget([
        'id'=>'grid',
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Актуальность',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $options['checked'] = (bool)$model->actual;
                    $options['class'] = 'check';
                    return $options;
                }],

            ['attribute' => 'name'],
            ['attribute' => 'levelNumber', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'branchString', 'encodeLabel' => false, 'format' => 'raw'],
            ['attribute' => 'pedCouncilDate', 'encodeLabel' => false, 'label' => 'Дата<br>пед. сов.'],
            ['attribute' => 'authorString', 'format' => 'html'],
            ['attribute' => 'capacity'],
            ['attribute' => 'focusString'],
            ['attribute' => 'fullDirectionName', 'encodeLabel' => false, 'label' => 'Тематическое<br>направление'],
        ],
    ]); ?>

</div>

    <?php
    $url = Url::toRoute('relevance-save');
    $urlBack = Url::toRoute(['relevance']);

    $this->registerJs(<<<JS
        $(document).ready(function () {
            $('#relevance-save').on('click', function () {
                /*var selectedIds = [];
                
                $('input.check:checked').each(function () {
                    selectedIds.push($(this).val());
                });*/
                
                if (true /* selectedIds.length > 0 */) {
                    // Отправляем POST-запрос на экшен контроллера
                    $.ajax({
                        type: 'POST',
                        url: "$url",
                        data: {/*ids: selectedIds*/},
                        success: function(response) {
                            let parsedResponse = JSON.parse(response); // Преобразуем строку JSON в объект
                            console.log(parsedResponse.success);
                            if (parsedResponse.success) {
                                window.location.href = "$urlBack";
                            } else {
                                alert('Ошибка: ' + parsedResponse.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Произошла ошибка: ' + xhr.responseText);
                        }
                    });
                } else {
                    alert('Не выбрано ни одного элемента!');
                }
            });
        });
        JS
    , View::POS_END);
    ?>
