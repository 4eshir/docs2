<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */
/* @var $scanFile */
/* @var $docFiles */
/* @var $appFiles */
/* @var $filesAnswer */


/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-out-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'document_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]])->label('Дата документа') ?>
    <?= $form->field($model, 'document_theme')->textInput(['maxlength' => true])->label('Тема документа') ?>

    <?php

    $params = [
        'prompt' => 'Выберите корреспондента',
        'onchange' => '
        $.post(
            "' . Url::toRoute('dependency-dropdown') . '", 
            {id: $(this).val()}, 
            function(res){
                var resArr = res.split("|split|");
                var elem = document.getElementsByClassName("pos");
                elem[0].innerHTML = resArr[0];
                elem = document.getElementsByClassName("com");
                elem[0].innerHTML = resArr[1];
            }
        );
    ',
    ];
    echo $form
        ->field($model, "correspondent_id")
        ->dropDownList(ArrayHelper::map($correspondentList, 'id', 'fullFio'),$params)
        ->label('ФИО корреспондента');

    ?>
    <div id="corr_div1">
        <?php
        $params = [
            'id' => 'position',
            'class' => 'form-control pos',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'position_id')
            ->dropDownList(ArrayHelper::map($availablePositions, 'id', 'name'), $params)
            ->label('Должность корреспондента (при наличии)');
        ?>
    </div>
    <div id="corr_div2">
        <?php
        $params = [
            'id' => 'company',
            'class' => 'form-control com',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'company_id')
            ->dropDownList(ArrayHelper::map($availableCompanies, 'id', 'name'), $params)
            ->label('Организация корреспондента');
        ?>
    </div>
    <div id="creator_div1">
        <?php
        $params = [
            'id' => 'creator',
            'class' => 'form-control cre',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'signed_id')
            ->dropDownList(ArrayHelper::map($correspondentList, 'id','fullFio'), $params)
            ->label('Кем подписан');
        ?>
    </div>

    <div id="creator_div2">
        <?php
        $params = [
            'id' => 'creator',
            'class' => 'form-control cre',
            'prompt' => '---',
        ];
        echo $form
            ->field($model, 'executor_id')
            ->dropDownList(ArrayHelper::map($correspondentList, 'id','fullFio'), $params)
            ->label('Кто исполнил');
        ?>
    </div>
    <?= $form->field($model, 'send_method')->dropDownList(Yii::$app->sendMethods->getList())->label('Способ отправки') ?>
    <?= $form->field($model, 'sent_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:d.m.Y',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2100',
        ]])->label('Дата отправки') ?>
    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>
    <?= $form->field($model, 'isAnswer')->checkbox(['id' => 'isAnswer', 'onchange' => 'checkAnswer()']) ?>
    <!-- <div id="dateAnswer" class="col-xs-4" <?= $model->isAnswer == 0 ? 'hidden' : '' ?>>
        <?= $form->field($model, 'dateAnswer')->widget(DatePicker::class, [
            'dateFormat' => 'php:d.m.Y',
            'language' => 'ru',
            'options' => [
                'placeholder' => 'Дата',
                'class'=> 'form-control',
                'autocomplete'=>'off'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '2000:2100',
            ]])->label('Крайний срок ответа')?>
    </div>
    -->
    <div id="filesAnswer" class="col-xs-4" <?= $model->isAnswer == 0 ? 'hidden' : '' ?>>
        <?php
        $params = [
            'prompt' => ''
        ];
       echo $form->field($model, "is_answer")
           ->dropDownList(
                ArrayHelper::map($filesAnswer,'id',function($model){

                    return  $model->local_number.' '.$model->document_theme;
                }),
                $params
            )
            ->label('Является ответом на');
        ?>
    </div>


    <div class="panel-body" style="padding: 0; margin: 0"></div>
    <?= $form->field($model, 'scanFile')->fileInput()
        ->label('Скан документа') ?>

    <?php if (is_array($scanFile) && count($scanFile) > 0): ?>
        <table class="table table-bordered">
        <?php foreach ($scanFile as $file): ?>
            <tr>
                <td><?= Html::a(basename($file->filepath), Url::to(['get-file', 'filepath' => $file->filepath])) ?></td>
                <td><?= Html::a('Удалить', Url::to(['delete-file', 'modelId' => $model->id, 'fileId' => $file->id])) ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?= $form->field($model, 'docFiles[]')
        ->fileInput(['multiple' => true])
        ->label('Редактируемые документы') ?>

    <?php if (is_array($docFiles) && count($docFiles) > 0): ?>
        <table class="table table-bordered">
            <?php foreach ($docFiles as $file): ?>
                <tr>
                    <td><?= Html::a(basename($file->filepath), Url::to(['get-file', 'filepath' => $file->filepath])) ?></td>
                    <td><?= Html::a('Удалить', Url::to(['delete-file', 'modelId' => $model->id, 'fileId' => $file->id])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?= $form->field($model, 'appFiles[]')
        ->fileInput(['multiple' => true])
        ->label('Приложения') ?>

    <?php if (is_array($appFiles) && count($appFiles) > 0): ?>
        <table class="table table-bordered">
            <?php foreach ($appFiles as $file): ?>
                <tr>
                    <td><?= Html::a(basename($file->filepath), Url::to(['get-file', 'filepath' => $file->filepath])) ?></td>
                    <td><?= Html::a('Удалить', Url::to(['delete-file', 'modelId' => $model->id, 'fileId' => $file->id])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    function checkAnswer()
    {
        var chkBox = document.getElementById('isAnswer');
        if (chkBox.checked)
        {
            $("#dateAnswer").removeAttr("hidden");
            $("#filesAnswer").removeAttr("hidden");
        }
        else
        {
            $("#dateAnswer").attr("hidden", "true");
            $("#filesAnswer").attr("hidden", "true");
        }
    }
</script>
<script>
    $("#corr").change(function() {
        if (this.value != '') {
            $("#corr_div1").attr("hidden", "true");
            $("#corr_div2").attr("hidden", "true");
        }
        else
        {
            $("#corr_div1").removeAttr("hidden");
            $("#corr_div2").removeAttr("hidden");
        }
    });
</script>