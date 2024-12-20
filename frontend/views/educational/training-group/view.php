<?php

use common\helpers\StringFormatter;
use frontend\forms\training_group\TrainingGroupCombinedForm;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model TrainingGroupCombinedForm */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Группа '.$this->title;
\yii\web\YiiAsset::register($this);
?>

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


<div class="training-group-view">

    <h1><?= Html::encode('Группа '.$this->title) ?>

    <p>
        <?= Html::a('Редактировать', ['base-form', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить группу?',
                'method' => 'post',
            ],
        ]) ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'branch', 'label' => 'Отдел производящий учет', 'format' => 'html', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->branch ? Yii::$app->branches->get($model->branch) : '';
            }],
            ['attribute' => 'number', 'label' => 'Номер группы'],
            ['attribute' => 'budget', 'label' => 'Форма обучения', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->budget == 1 ? 'Бюджет' : 'Внебюджет';
            }],
            ['attribute' => 'trainingProgram', 'format' => 'html', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->trainingProgram ? $model->trainingProgram->name : '';
            }],
            ['attribute' => 'network', 'label' => 'Сетевая форма обучения', 'value' => function (TrainingGroupCombinedForm $model){
                return $model->network == 1 ? 'Да' : 'Нет';
            }],
            ['attribute' => 'teachersList', 'format' => 'html', 'value' => function (TrainingGroupCombinedForm $model){
                $newTeachers = [];
                foreach ($model->teachers as $teacher) {
                    /** @var TeacherGroupWork $teacher */
                    $newTeachers[] = StringFormatter::stringAsLink($teacher->teacherWork->getFIO(PeopleWork::FIO_FULL), Url::to(['dictionaries/people/view', 'id' => $teacher->teacherWork->people_id]));
                }
                return implode('<br>', $newTeachers);
            }],
            ['attribute' => 'startDate', 'label' => 'Дата начала занятий'],
            ['attribute' => 'endDate', 'label' => 'Дата окончания занятий'],
            ['attribute' => 'photoFiles', 'value' => function (TrainingGroupCombinedForm $model) {
                return $model->photoFiles;
            }, 'format' => 'raw'],
            ['attribute' => 'presentationFiles', 'value' => function (TrainingGroupCombinedForm $model) {
                return $model->presentationFiles;
            }, 'format' => 'raw'],
            ['attribute' => 'workFiles', 'value' => function (TrainingGroupCombinedForm $model) {
                return $model->workMaterialFiles;
            }, 'format' => 'raw'],
            /*
            ['attribute' => 'countParticipants', 'label' => 'Количество учеников', 'format' => 'html'],
            ['attribute' => 'participantNames', 'value' => '<button class="accordion">Показать состав группы</button><div class="panel">'.$model->participantNames.'</div>', 'format' => 'raw'],
            ['attribute' => 'countLessons', 'label' => 'Количество занятий в расписании', 'format' => 'html'],
            ['attribute' => 'lessonDates', 'value' => '<button class="accordion">Показать расписание группы</button><div class="panel">'.$model->lessonDates.'</div>', 'format' => 'raw'],
            ['attribute' => 'manHoursPercent', 'format' => 'raw', 'label' => 'Выработка человеко-часов'],
            ['attribute' => 'journalLink', 'format' => 'raw', 'label' => 'Журнал'],
            ['attribute' => 'ordersName', 'format' => 'raw'],
            */

            //['attribute' => 'openText', 'label' => 'Темы занятий перенесены (при наличии)'],
        ],
    ]) ?>

    <h4><u>Ученики</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'participants', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyParticipants());
            }],
        ],
    ]) ?>

    <h4><u>Занятия</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'lessons', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyLessons());
            }],
        ],
    ]) ?>

    <h4><u>Сведения о защите работ</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'protection_date'],
            ['attribute' => 'themes', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyThemes());
            }],
            ['attribute' => 'experts', 'format' => 'raw', 'value' => function (TrainingGroupCombinedForm $model) {
                return implode('<br>', $model->getPrettyExperts());
            }],
        ],
    ]) ?>

</div>


<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            /* Toggle between adding and removing the "active" class,
            to highlight the button that controls the panel */
            this.classList.toggle("active");

            /* Toggle between hiding and showing the active panel */
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
</script>