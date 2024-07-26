<?php

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
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
            ['label' => 'Должность корреспондента', 'attribute' => 'position_id', 'value' => function(DocumentInWork $model) {
                return $model->positionWork ? $model->positionWork->name : '';
            }],
            ['label' => 'Организация корреспондента', 'attribute' => 'company_id', 'value' => function(DocumentInWork $model) {
                return $model->companyWork ? $model->companyWork->name : '';
            }],
            ['label' => 'Тема документа', 'attribute' => 'document_theme'],
            ['label' => 'Способ получения', 'attribute' => 'send_method', 'value' => Yii::$app->sendMethods->get($model->send_method)],
            ['label' => 'Скан документа', 'attribute' => 'scan', 'value' => function (DocumentInWork $model) {
                return implode('<br>', $model->getFileLinks(FilesHelper::TYPE_SCAN));
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'docFiles', 'value' => function ($model) {
                return implode('<br>', $model->getFileLinks(FilesHelper::TYPE_DOC));
            }, 'format' => 'raw'],
            ['label' => 'Приложения', 'attribute' => 'applications', 'value' => function ($model) {
                return implode('<br>', $model->getFileLinks(FilesHelper::TYPE_APP));
            }, 'format' => 'raw'],
            ['label' => 'Ключевые слова', 'attribute' => 'key_words'],
            ['attribute' => 'needAnswer', 'label' => 'Ответ', 'value' => function(DocumentInWork $model){
                return $model->getNeedAnswer(StringFormatter::FORMAT_LINK);
            }, 'format' => 'raw'],
            ['label' => 'Создатель карточки', 'attribute' => 'creator_id', 'value' => function(DocumentInWork $model) {
                return $model->correspondentWork ? $model->correspondentWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '';
            }],
            ['label' => 'Последний редактор', 'attribute' => 'last_update_id', 'value' => function(DocumentInWork $model) {
                return $model->lastUpdateWork ? $model->lastUpdateWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '';
            }],
        ],
    ]) ?>

</div>
