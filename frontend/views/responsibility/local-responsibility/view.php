<?php

use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\responsibility\LegacyResponsibleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LocalResponsibilityWork */
/* @var $history LegacyResponsibleWork */

$this->title = $model->peopleStampWork->surname . ' ' . Yii::$app->responsibilityType->get($model->responsibility_type);
if ($model->quant !== null) {
    $this->title .= ' №' . $model->quant;
}
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="local-responsibility-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную ответственность?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h4><u>Общая информация</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'responsibility_type', 'format' => 'raw', 'value' => function (LocalResponsibilityWork $model){
                return Yii::$app->responsibilityType->get($model->responsibility_type);
            }],
            ['attribute' => 'branch', 'format' => 'raw', 'value' => function (LocalResponsibilityWork $model){
                return Yii::$app->branches->get($model->branch);
            }],
            ['attribute' => 'auditorium', 'format' => 'raw', 'value' => function (LocalResponsibilityWork $model){
                return $model->auditoriumWork->name;
            }],
            ['attribute' => 'quant', 'format' => 'raw'],
            ['attribute' => 'people', 'format' => 'raw', 'value' => function (LocalResponsibilityWork $model){
                return $model->peopleStampWork ?
                    StringFormatter::stringAsLink(
                        $model->peopleStampWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS),
                        Url::to(['/dictionaries/people/view', 'id' => $model->peopleStampWork->people_id])
                    ) :
                    '---';
            }],
            ['attribute' => 'regulation', 'format' => 'raw', 'value' => function (LocalResponsibilityWork $model){
                return StringFormatter::stringAsLink(
                    $model->regulationWork->name,
                    Url::to(['/regulation/regulation/view', 'id' => $model->regulation_id])
                );
            }],
            ['label' => 'Файлы', 'attribute' => 'filesList', 'value' => function (LocalResponsibilityWork $model) {
                return implode('<br>', ArrayHelper::getColumn($model->getFileLinks(FilesHelper::TYPE_OTHER), 'link'));
            }, 'format' => 'raw'],
        ],
    ]) ?>
    <br>
    <h4><u>История ответственности</u></h4>
    <?php /*= DetailView::widget([
        'model' => $history,
        'attributes' => [
            ['attribute' => 'order', 'format' => 'raw', 'label' => 'Приказ', 'value' => function (LegacyResponsibleWork $history){
                return StringFormatter::stringAsLink(
                    $history->orderWork->getNameWithNumber(),
                    Url::to(['/order/order-main/view', 'id' => $history->order_id])
                );
            }],
        ],
    ]) */?>

</div>
