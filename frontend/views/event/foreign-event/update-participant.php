<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\work\TeacherParticipantBranchWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\TeacherParticipantWork */


$this->title = 'Редактировать: ' . $model->participantWork->fullName;

if ($back == 'event')
{
    $this->params['breadcrumbs'][] = ['label' => 'Учет достижений в мероприятиях', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => mb_substr($model->foreignEventWork->name, 0, 25, 'UTF-8').'...', 'url' => ['foreign-event/update', 'id' => $model->foreignEventWork->id]];
}
else
{
    $this->params['breadcrumbs'][] = ['label' => 'Приказы', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => mb_substr($model->foreignEventWork->documentOrderWork->documentNumberString.' '.$model->foreignEventWork->documentOrderWork->order_name, 0, 35, 'UTF-8').'...', 'url' => ['document-order/update', 'id' => $model->foreignEventWork->documentOrderWork->id]];
}
$this->params['breadcrumbs'][] = ['label' => $model->participantWork->fullName, 'url' => ['foreign-event-participants/view', 'id' => $model->participant_id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="teacher-participant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php

    if ($back == 'event')
    {
        $teacher = $model->teacherWork->fullName;
        $teacher2 = $model->teacher2Work->fullName;
        echo $form->field($model, 'teacher')->textInput(['readonly' => true, 'value' => $teacher])->label('ФИО педагогов');
        echo $form->field($model, 'teacher2')->textInput(['readonly' => true, 'value' => $teacher2])->label(false);
    }
    else
    {
        $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
        $params = [
            'prompt' => ''
        ];
        echo $form->field($model, 'teacher_id')->dropDownList($items,$params)->label('ФИО педагогов');
        echo $form->field($model, 'teacher2_id')->dropDownList($items,$params)->label(false);
    }


    ?>

    <?php
    if ($back == 'event')
    {
        $focus = \app\models\work\FocusWork::find()->where(['id' => $model->focus])->one();
        echo $form->field($model, 'foc')->textInput(['readonly' => true, 'value' => $focus->name])->label('Направленность');
    }
    else
    {
        $focuses = \app\models\work\FocusWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($focuses,'id','name');
        $params = [
            'prompt' => ''
        ];
        echo $form->field($model, 'focus')->dropDownList($items,$params)->label('Направленность');
    }
    ?>

    <?php
    if ($back == 'event')
    {
        echo $form->field($model, 'nomination')->textInput(['readonly' => true, 'value' => $model->nomination])->label('Номинация');
    }
    else
    {
        $noms = \app\models\work\TeacherParticipantWork::find()->where(['foreign_event_id' => $model->foreign_event_id])->all();
        $nomsArr = [];
        foreach ($noms as $nom)
            if (!in_array($nom->nomination, $nomsArr) && $nom->nomination != null)
                $nomsArr[] = $nom;
        $items = \yii\helpers\ArrayHelper::map($nomsArr,'nomination','nomination');
        $params = [
            'prompt' => '--'
        ];
        echo $form->field($model, 'nomination')->dropDownList($items, $params)->label('Номинация');
    }

    ?>

    <?php

    if ($back == 'event')
        echo '<fieldset disabled>';

    $branchs = \app\models\work\BranchWork::find()->orderBy(['id' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($branchs, 'id', 'name');
    echo $form->field($model, 'branchs')->checkboxList(
        $items, ['class' => 'base',
            'item' => function ($index, $label, $name, $checked, $value) {
                if ($checked == 1) $checked = 'checked';
                return
                    '<div class="checkbox" class="form-control">
                        <label style="margin-bottom: 0px" for="branch-' . $index .'">
                            <input onclick="ClickBranch(this, '.$index.')" id="branch-'. $index .'" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                            '. $label .'
                        </label>
                    </div>';
            }]
    )->label('<u>Отдел(-ы)</u>');

    if ($back == 'event')
        echo '</fieldset>';

    ?>

    <?php 

        $realizes = \app\models\work\AllowRemoteWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($realizes,'id','name');
        $tps = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $model->id])->all();
        $flag = false;
        foreach ($tps as $tp)
            if ($tp->branch_id == 7)
                $flag = true;
        if ($flag)
            $params = [
                //'prompt' => ''
                'id' => 'allow_id',
            ];
        else
            $params = [
                //'prompt' => ''
                'disabled' => true,
                'id' => 'allow_id',
            ];

        

        echo $form->field($model, 'allow_remote_id')->dropDownList($items,$params)->label('Форма реализации'); 
    ?>

    <?php
        $teamName = \app\models\work\TeamNameWork::find()->where(['foreign_event_id' => $model->foreign_event_id])->all();
        $items = \yii\helpers\ArrayHelper::map($teamName,'id','name');
        $params = [
            'prompt' => '--',
        ];
        echo $form->field($model, 'team')->dropDownList($items,$params)->label('Команда')
    ?>

    <?= $form->field($model, 'file')->fileInput()->label('Представленные материалы') ?>

    <?php
    $partFiles = \app\models\work\ParticipantFilesWork::find()->where(['participant_id' => $model->participant_id])->andWhere(['foreign_event_id' => $model->foreign_event_id])->one();
    if ($partFiles !== null)
        echo '<h5>Загруженный файл: '.Html::a($partFiles->filename, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $partFiles->filename, 'type' => 'participants'])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['foreign-event/delete-file', 'fileName' => $partFiles->filename, 'modelId' => $partFiles->id, 'type' => 'participants'])).'</h5><br>';
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>

    function ClickBranch($this, $index)
    {
        if ($index == 5)
        {
            
            let second_gen = document.getElementById('allow_id');
            console.log($this.checked);
            if (second_gen.hasAttribute('disabled') && $this.checked == true)
                second_gen.removeAttribute('disabled');
            else
            {
                second_gen.value = 1;
                second_gen.setAttribute('disabled', 'disabled');
            }
        }
        
    }
</script>