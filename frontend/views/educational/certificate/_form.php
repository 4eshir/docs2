<?php

use frontend\forms\certificate\CertificateForm;
use frontend\models\work\educational\CertificateWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model CertificateForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
if(isset($_GET['group_id'])) {
    $model->group_id = $_GET['group_id'];
}
?>

<div class="certificate-form">

    <div style="margin: 0 103%;">
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Если обучающийся не отображен в списке проверьте следующие возможные причины:
                                                            &#10   &#10102 У обучающегося уже есть сертификат об окончании обучения в данной учебной группе
                                                            &#10   &#10103 Обучающийся отчислен из учебной группы
                                                            &#10   &#10104 У обучающегося отсутствует галочка успешного окончания в журнале" >❔</div>
    </div>

    <?php $form = ActiveForm::begin([
        'options' => ['target' => '_blank', 'id' => 'form1']
    ]); ?>


    <?= $form->field($model, 'certificate_template_id')
        ->dropDownList(ArrayHelper::map($model->templates,'id','name'))
        ->label('Шаблон сертификатов'); ?>



    <?php
    $params = [
        'prompt' => '---',
        'id' => 'groupList',
        'onchange' => 'changeGroup()',
    ];

    echo $form->field($model, 'group_id')
        ->dropDownList($model->groups, $params)
        ->label('Группа');

    ?>

    <?php

    $cert = \app\models\work\CertificatWork::find()->all();

    $cIds = [];
    foreach($cert as $one) $cIds[] = $one->training_group_participant_id;

    $tps = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['trainingGroup.archive' => 0])->andWhere(['status' => 0])->andWhere(['NOT IN', 'training_group_participant.id', $cIds])->andWhere(['success' => 1])->all();

    echo '<table class="table table-striped">';
    foreach($tps as $tp)
    {
        echo '<tr>';
        $style = '';
        if ($model->group_id != $tp->training_group_id)
            $style = '" style="display: none"';
        echo '<td class="parts '.$tp->training_group_id.$style.'>'.$form->field($model, 'participant_id[]')->checkbox(['label' => $tp->participantWork->fullName, 'value' => $tp->id])->label(false).'</td>';

        echo '</tr>';
    }
    echo '</table>';

    ?>

    <div class="form-group">
        <?php
        echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    function changeGroup()
    {
        let elem = document.getElementById('groupList');
        let parts = document.getElementsByClassName('parts');
        for (let i = 0; i < parts.length; i++)
            parts[i].style.display = 'none';

        parts = document.getElementsByClassName(elem.value);
        for (let i = 0; i < parts.length; i++)
            parts[i].style.display = 'block';
    }

    document.getElementById("form1").onsubmit = function()
    {
        //window.open("https://google.ru", '_blank');
        //window.location.href = "https://docs/index.php?r=certificat/index";
        setTimeout(redirectHandler, 500);
    }

    function redirectHandler()
    {
        window.location = "index.php?r=certificat/index";
    }
</script>