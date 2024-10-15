<?php

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentInWork */

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

    <div class="card">
        <div class="card-block-1">
            <div>
                <title>Основное</title>
                <div>Имя</div>
                <div>Вид</div>
                <div>Тема</div>
            </div>
            <div>
                <title>От кого</title>
                <div>Корреспондент</div>
                <div>Дата и номер</div>
                <div>ФИО и должность</div>
            </div>
            <div>
                <title>Ответ</title>
                <div>Ответственный и срок</div>
            </div>
        </div>
        <div class="card-block-2">
            <div>
                <title>Дата и номер</title>
                <div>Порядковый номер</div>
                <div>Дата</div>
                <div>Способ отправки</div>
            </div>
            <div>
                <title>Ключевые слова</title>
                <div>слова-слова-слова</div>
            </div>
            <div>
                <title>Файлы</title>
                <div>Сканы</div>
                <div>Редактируемые</div>
                <div>Приложения</div>
            </div>
        </div>
    </div>
<br>
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
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_SCAN), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'docFiles', 'value' => function (DocumentInWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_DOC), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Приложения', 'attribute' => 'applications', 'value' => function (DocumentInWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_APP), 'link'));
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
