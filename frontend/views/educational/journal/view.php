<?php

use common\helpers\DateFormatter;
use frontend\forms\journal\JournalForm;
use frontend\forms\journal\ThematicPlanForm;
use frontend\models\work\dictionaries\PersonInterface;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model JournalForm */
/* @var $plan ThematicPlanForm */
/* @var $buttonsAct */
/* @var $otherButtonsAct */

$this->title = 'Электронный журнал';
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_INDEX]];
$this->params['breadcrumbs'][] = ['label' => 'Группа ' . $model->getTrainingGroupNumber(), 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->groupId]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="journal-view">
    <div class="substrate">
        <div class="flexx">
            <h1>
                <?= Html::encode('Журнал ' . $model->getTrainingGroupNumber()) ?>
            </h1>
            <h3>
                <?= $model->getRawArchiveGroup(); ?>
            </h3>
        </div>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct; ?>
            </div>
            <div class="flexx">
                <?= $otherButtonsAct; ?>
            </div>
        </div>
    </div>

    <div class="card">
        
    </div>
</div>





<div class="journal">
    <?= Html::a('Редактировать журнал', ['update', 'id' => $model->groupId], ['class' => 'btn btn-primary']) ?>
    <table class="table table-bordered">
        <?php foreach ($model->participantLessons as $participantLesson): ?>
            <tr>
                <td>
                    <?= $participantLesson->participant->getFIO(PersonInterface::FIO_SURNAME_INITIALS); ?>
                </td>
                <?php foreach ($participantLesson->lessonIds as $lesson): ?>
                    <td>
                        <?= $lesson->getPrettyStatus() ?>
                    </td>
                <?php endforeach; ?>
                <td>
                    <?= $participantLesson->groupProjectThemesWork->projectThemeWork->name; ?>
                </td>
                <td>
                    <?= $participantLesson->points; ?>
                </td>
                <td>
                    <?= $participantLesson->successFinishing; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?= Html::a('Редактировать тематический план', ['edit-plan', 'id' => $model->groupId], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Очистить тематический план', ['delete-plan', 'id' => $model->groupId], ['class' => 'btn btn-danger']) ?>
    <table class="table table-bordered">
        <?php foreach ($plan->lessonThemes as $index => $lessonTheme): ?>
            <tr>
                <td>
                    <?=
                        DateFormatter::format(
                            $lessonTheme->trainingGroupLessonWork->lesson_date,
                            DateFormatter::Ymd_dash,
                            DateFormatter::dmY_dot
                        )
                    ?>
                </td>
                <td>
                    <?= $lessonTheme->thematicPlanWork->theme ?>
                </td>
                <td>
                    <?= $lessonTheme->teacherWork->peopleWork->getFIO(PersonInterface::FIO_FULL) ?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
</div>