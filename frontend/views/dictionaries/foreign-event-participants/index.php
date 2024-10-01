<?php

use frontend\models\search\SearchForeignEventParticipants;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel SearchForeignEventParticipants */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Участники деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['dictionaries/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="foreign-event-participants-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить нового участника деятельности', ['create'], ['class' => 'btn btn-success']) ?> <?= Html::a('Загрузить участников из файла', ['file-load'], ['class' => 'btn btn-primary']) ?> <?= Html::a('Проверить участников на некорректные данные', ['check-correct'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?php
    echo '<div style="margin-bottom: 10px">'.Html::a('Показать участников с некорректными данными', \yii\helpers\Url::to(['foreign-event-participants/index', 'sort' => '1']), ['class' => 'btn btn-danger', 'style' => 'margin-right: 5px;']);
    echo Html::a('Показать участников с ограничениями на разглашение ПД', \yii\helpers\Url::to(['foreign-event-participants/index', 'sort' => '2']), ['class' => 'btn btn-info']).'</div>';
    ?>

    <?php

    $gridColumns = [
            'surname',
            'firstname',
            'patronymic',
            ['attribute' => 'sex', 'value' => function(ForeignEventParticipantsWork $model) {
                return $model->getSexString();
            }],
            ['attribute' => 'birthdate', 'value' => function($model){return date("d.m.Y", strtotime($model->birthdate));}],
            ['attribute' => 'eventsExcel', 'label' => 'Мероприятия', 'format' => 'raw'],
            ['attribute' => 'studiesExcel', 'label' => 'Учебные группы'],
            ['class' => 'yii\grid\ActionColumn'],

    ];
    echo '<div style="margin-bottom: 10px"><b>Скачать файл </b>';
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'options' => [
            'padding-bottom: 100px',
        ]
    ]);
    echo '</div>';

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            if ($data['sex'] == 2) {
                return ['class' => 'danger'];
            }
            else if (($data['is_true'] == 0 || $data['is_true'] == 2) && $data['guaranteed_true'] !== 1) {
                return ['class' => 'warning'];
            }
            else {
                return ['class' => 'default'];
            }
        },
        'columns' => [
            'surname',
            'firstname',
            'patronymic',
            ['attribute' => 'sex', 'value' => function(ForeignEventParticipantsWork $model) {
                return $model->getSexString();
            }],
            ['attribute' => 'birthdate', 'value' => function($model){return date("d.m.Y", strtotime($model->birthdate));}],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <div class="form-group">
        <!--<a class="btn btn-danger" href="/index.php?r=training-group%2Findex&archive=">Сохранить архив</a>-->
        <?php echo Html::a("Слияние участников деятельности", \yii\helpers\Url::to(['foreign-event-participants/merge-participant']), ['class'=>'btn btn-success']); ?>
    </div>

</div>
