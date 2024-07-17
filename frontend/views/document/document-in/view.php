<?php

use common\helpers\DateFormatter;
use common\helpers\FilesHelper;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\general\PeopleWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\work\document_in_out\DocumentInWork */

$this->title = $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>


<div class="document-in-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => '№ п/п', 'attribute' => 'fullNumber'],
            ['label' => 'Дата поступления документа', 'attribute' => 'local_date', 'value' => function(DocumentInWork $model) {
                return DateFormatter::format($model->local_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }],
            ['label' => 'Дата входящего документа', 'attribute' => 'real_date', 'value' => function(DocumentInWork $model) {
                return DateFormatter::format($model->real_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }],
            ['label' => 'Регистрационный номер входящего документа', 'attribute' => 'real_number'],
            ['label' => 'ФИО корреспондента', 'attribute' => 'correspondent_id', 'value' => function(DocumentInWork $model) {
                return $model->correspondentWork ? $model->correspondentWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '';
            }],
            ['label' => 'Должность корреспондента', 'attribute' => 'position_id', 'value' => $model->positionWork->name],
            ['label' => 'Организация корреспондента', 'attribute' => 'company_id', 'value' => $model->companyWork->name],
            ['label' => 'Тема документа', 'attribute' => 'document_theme'],
            ['label' => 'Способ получения', 'attribute' => 'send_method', 'value' => Yii::$app->sendMethods->get($model->send_method)],
            ['label' => 'Скан документа', 'attribute' => 'scan', 'value' => function (DocumentInWork $model) {
                return implode('<br>', $model->getFileLinks(FilesHelper::TYPE_SCAN));
            }, 'format' => 'raw'],
            /*['label' => 'Редактируемые документы', 'attribute' => 'docFiles', 'value' => function ($model) {
                $split = explode(" ", $model->doc);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['document-in/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'docs'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Приложения', 'attribute' => 'applications', 'value' => function ($model) {
                $split = explode(" ", $model->applications);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['document-in/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'apps'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
            ['attribute' => 'needAnswer', 'label' => 'Ответ', 'value' => function($model){
                $links = \app\models\work\InOutDocsWork::find()->where(['document_in_id' => $model->id])->one();
                if ($links == null)
                    return '';
                if ($links->document_out_id == null)
                    return 'Требуется ответ';
                else
                    return Html::a('Исходящий документ "'.\app\models\work\DocumentOutWork::find()->where(['id' => $links->document_out_id])->one()->document_theme.'"',
                        \yii\helpers\Url::to(['docs-out/view', 'id' => \app\models\work\DocumentOutWork::find()->where(['id' => $links->document_out_id])->one()->id]));
            }, 'format' => 'raw'],
            ['label' => 'Создатель карточки', 'attribute' => 'creator_id', 'value' => $model->creatorWork->secondname.' '.mb_substr($model->creatorWork->firstname, 0, 1).'. '.mb_substr($model->creatorWork->patronymic, 0, 1).'.'],
            ['label' => 'Последний редактор', 'attribute' => 'last_edit_id', 'value' => $model->lastEditWork->secondname.' '.mb_substr($model->lastEditWork->firstname, 0, 1).'. '.mb_substr($model->lastEditWork->patronymic, 0, 1).'.'],
        */],
    ]) ?>

</div>
