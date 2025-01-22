<?php

use common\helpers\StringFormatter;
use frontend\models\search\SearchCertificate;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\general\PeopleWork;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchCertificate */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="certificat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        echo Html::a('Добавить сертифкат(-ы)', ['create'], ['class' => 'btn btn-success'])
        ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div style="margin-bottom: 10px;">
        <?php

        $gridColumns = [
            ['attribute' => 'certificate_number', 'format' => 'raw', 'value' => function(CertificateWork $model){
                return StringFormatter::stringAsLink($model->certificate_number, Url::to(['view', 'id' => $model->id]));
            }],
            ['attribute' => 'certificate_template_id', 'format' => 'raw', 'value' => function(CertificateWork $model){
                return StringFormatter::stringAsLink($model->certificateTemplatesWork->name, Url::to(['/educational/certificate-template/view', 'id' => $model->id]));
            }],
            ['attribute' => 'certificatTemplateName', 'format' => 'raw'],
            ['attribute' => 'participantName', 'label' => 'Отдел', 'format' => 'raw'],
            ['attribute' => 'participantGroup', 'format' => 'raw'],
            ['attribute' => 'participantProtection', 'format' => 'raw'],

        ];
        echo '<b>Скачать файл </b>';
        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'options' => [
                'padding-bottom: 100px',
            ]
        ]);

        ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '№ п/п'],
            ['attribute' => 'certificate_number', 'format' => 'raw', 'value' => function(CertificateWork $model){
                return StringFormatter::stringAsLink($model->certificate_number, Url::to(['view', 'id' => $model->id]));
            }],
            ['attribute' => 'certificate_template_id', 'format' => 'raw', 'value' => function(CertificateWork $model){
                return StringFormatter::stringAsLink(
                        $model->certificateTemplatesWork->name,
                        Url::to(['/educational/certificate-template/view', 'id' => $model->certificate_template_id])
                );
            }],
            ['attribute' => 'participant_id', 'format' => 'raw', 'value' => function(CertificateWork $model){
                return StringFormatter::stringAsLink(
                    $model->trainingGroupParticipantWork->participantWork->getFIO(PeopleWork::FIO_FULL),
                    Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $model->trainingGroupParticipantWork->participant_id])
                );
            }],
            ['attribute' => 'training_group_id', 'format' => 'raw', 'value' => function(CertificateWork $model){
                return StringFormatter::stringAsLink(
                    $model->trainingGroupParticipantWork->trainingGroupWork->number,
                    Url::to(['/educational/training-group/view', 'id' => $model->trainingGroupParticipantWork->training_group_id])
                );
            }],
            ['attribute' => 'protection_date', 'format' => 'raw', 'value' => function(CertificateWork $model){
                return $model->trainingGroupParticipantWork->trainingGroupWork->protection_date;
            }],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
