<?php

use common\helpers\html\HtmlBuilder;
use frontend\models\work\order\DocumentOrderWork;
use frontend\models\work\order\OrderTrainingWork;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model OrderTrainingWork */
/* @var $modelResponsiblePeople */
/* @var $groups */
/* @var $participants */
/* @var $error */
$this->title = $model->order_name;
$this->params['breadcrumbs'][] = ['label' => 'Приказы об образовательной деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-training-view">
    <?php
    switch ($error) {
        case DocumentOrderWork::ERROR_DATE_PARTICIPANT:
            echo HtmlBuilder::createMessage(HtmlBuilder::TYPE_WARNING,'Невозможно применить действие к ученикам.', 'Ошибка выбора даты приказа');
            break;
        case DocumentOrderWork::ERROR_RELATION:
            echo HtmlBuilder::createMessage(HtmlBuilder::TYPE_WARNING,'Невозможно применить действие к ученикам.', 'Выбранные обучающиеся задействованы в других приказах');
            break;
    }
    ?>
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
        <?= Html::a('Сгенерировать документ', ['generate-order', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= HtmlBuilder::createErrorsBlock(DocumentOrderWork::tableName(), $model->id) ?>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Регистрационный номер приказа', 'value' => function (OrderTrainingWork $model) {
                return $model->getNumberPostfix();
            }],
            ['label' => 'Наименование приказа', 'attribute' => 'order_name'],
            ['label' => 'Дата приказа', 'attribute' => 'order_date', 'value' => function (OrderTrainingWork $model) {
                return DateFormatter::format($model->order_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot);
            }],

            ['label' => 'Проект вносит', 'attribute' => 'bring_id', 'value' => function (OrderTrainingWork $model) {
                return $model->bringWork ? $model->bringWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS) : '';
            }],
            ['label' => 'Исполнитель', 'attribute' => 'executor_id', 'value' => function (OrderTrainingWork $model) {
                return $model->executorWork ? $model->executorWork->getFullFio() : '';
            }],
            ['label' => 'Ответственные', 'value' => $modelResponsiblePeople, 'format' => 'raw'],
            ['label' => 'Скан документа', 'attribute' => 'scan', 'value' => function (OrderTrainingWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_SCAN), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Редактируемые документы', 'attribute' => 'doc', 'value' => function (OrderTrainingWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_DOC), 'link'));
            }, 'format' => 'raw'],
            ['label' => 'Создатель карточки', 'attribute' => 'creator_id', 'value' => function (OrderTrainingWork $model) {
                return $model->creatorWork ? $model->creatorWork->getFullName() : '';
            }],
            ['label' => 'Последний редактор', 'attribute' => 'last_update_id', 'value' => function (OrderTrainingWork $model) {
                return $model->lastUpdateWork ? $model->lastUpdateWork->getFullName() : '';
            }],
        ],
    ]) ?>
    <br>
    <br>
    <div>
        <h3> Данные о приказе</h3>
    </div>
    <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Группы в приказе',
                    'value' => $groups,
                    'format' => 'raw'

                ],
                [
                    'label' => 'Обучающиеся',
                    'value' => $participants,
                    'format' => 'raw'
                ],
            ]
        ]);
    ?>
</div>
