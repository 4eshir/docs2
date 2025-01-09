<?php

use app\components\DropDownPosition;
use app\components\DynamicWidget;
use common\components\dictionaries\base\BranchDictionary;
use frontend\forms\journal\JournalForm;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\educational\journal\VisitWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model JournalForm */

?>

<div class="journal-edit">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="journal-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'groupId')->hiddenInput()->label(false) ?>

        <?php foreach ($model->participantLessons as $participantLesson): ?>
            <h4>Participant ID: <?= $participantLesson->participantId ?></h4>
            <?php foreach ($participantLesson->lessonIds as $index => $lesson): ?>
                <?= $form->field($lesson, "[$participantLesson->participantId][$index]status")
                    ->dropDownList([
                        VisitWork::NONE => '---',
                        VisitWork::ATTENDANCE => 'Я',
                        VisitWork::NO_ATTENDANCE => 'Н',
                        VisitWork::DISTANCE => 'Д'
                    ])
                    ->label("Lesson ID: {$lesson->lessonId}"); ?>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
