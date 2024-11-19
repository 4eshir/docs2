<?php

use common\helpers\files\FilesHelper;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrainingProgramWork */
/* @var $thematicPlan array */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="training-program-view">

<style>
    .accordion {
        background-color: #3680b1;
        color: white;
        cursor: pointer;
        padding: 8px;
        width: 100%;
        text-align: left;
        border: none;
        outline: none;
        transition: 0.4s;
        border-radius: 5px;
    }

    /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
    .active, .accordion:hover {

    }

    /* Style the accordion panel. Note: hidden by default */
    .panel {
        padding: 0 18px;
        background-color: white;
        display: none;
        overflow: hidden;
    }

    .hoverless:hover {
        cursor: default;
    }
</style>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить программу?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            ['attribute' => 'level', 'value' => function (TrainingProgramWork $model) {
                return $model->level + 1;
            }],
            'ped_council_date',
            'ped_council_number',
            ['attribute' => 'compilers', 'format' => 'html'],
            'capacity',
            'student_left_age',
            'student_right_age',
            ['attribute' => 'focus', 'value' => function (TrainingProgramWork $model) {
                return Yii::$app->focus->get($model->focus);
            }, 'format' => 'raw'],
            ['attribute' => 'fullDirectionName', 'label' => 'Тематическое направление'],
            'hour_capacity',
            ['attribute' => 'themesPlan', 'value' =>
                '<button class="accordion">Показать учебно-тематический план</button><div class="panel">'.implode('<br>', ArrayHelper::getColumn($thematicPlan, 'theme')).'</div>',
                'format' => 'raw', 'label' => 'Учебно-тематический план'],
            ['attribute' => 'branches', 'format' => 'raw'],
            ['attribute' => 'allowRemote', 'format' => 'raw'],
            ['attribute' => 'mainFile', 'value' => function (TrainingProgramWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_MAIN), 'link'));
            }, 'format' => 'raw'],
            ['attribute' => 'docFiles', 'value' => function ($model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_DOC), 'link'));
            }, 'format' => 'raw'],
            ['attribute' => 'contractFile', 'value' => function ($model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_CONTRACT), 'link'));
            }, 'format' => 'raw'],
            ['attribute' => 'certificateType', 'label' => 'Итоговая форма контроля', 'value' => function (TrainingProgramWork $model) {
                return Yii::$app->certificateType->get($model->certificate_type);
            }],
            ['attribute' => 'description', 'label' => 'Описание'],
            'key_words',
            ['attribute' => 'actual', 'value' => function($model) {return $model->actual == 0 ? 'Нет' : 'Да';}, 'label' => 'Образовательная программа актуальна'],
            //['attribute' => 'linkGroups', 'value' => '<div style="float: left; width: 20%; height: 100%; line-height: 250%">'.$model->getGroupsCount().'</div><div style="float: left; width: 80%"><button class="accordion" style="display: flex; float: left">Показать учебные группы</button><div class="panel">'.$model->getLinkGroups().'</div></div>', 'format' => 'raw', 'label' => 'Учебные группы'],
            ['attribute' => 'creatorString', 'format' => 'raw'],
            ['attribute' => 'lastUpdateString', 'format' => 'raw'],
        ],
    ]) ?>

</div>

<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");

            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
</script>