<?php

use common\components\dictionaries\base\BranchDictionary;
use frontend\forms\event\EventParticipantForm;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model EventParticipantForm */


$this->title = 'Редактировать: ' . $model->actParticipant->team_name_id ?
    'Команда ' . $model->actParticipant->teamNameWork->name :
    $model->actParticipant->getParticipants()[0]->participantWork->getFIO(PeopleWork::FIO_FULL);

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-participant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'teacher')->textInput(['readonly' => true, 'value' => $model->actParticipant->teacher_id])->label('ФИО педагогов'); ?>
    <?= $form->field($model, 'teacher2')->textInput(['readonly' => true, 'value' => $model->actParticipant->teacher2_id])->label(false); ?>
    <?= $form->field($model, 'focus')->textInput(['readonly' => true, 'value' => $model->actParticipant->focus])->label('Направленность'); ?>
    <?= $form->field($model, 'nomination')->textInput(['readonly' => true, 'value' => $model->actParticipant->nomination])->label('Номинация'); ?>

    <fieldset disabled>
        <?=
            $form->field($model, 'branches')->checkboxList(
                Yii::$app->branches->getList(), ['class' => 'base',
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
            )->label('<u>Отдел(-ы)</u>')
        ?>
    </fieldset>

    <?php
        if (in_array(BranchDictionary::COD, $model->branches))
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

        

        echo $form->field($model, 'allow_remote_id')->dropDownList(
                Yii::$app->allowRemote->getList(),$params)->l abel('Форма реализации');
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