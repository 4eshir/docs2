<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\EventWork */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Мероприятия', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить это мероприятие?',
                'method' => 'post',
            ],
        ]) ?>
        <?php
        $error = $model->getErrorsWork();
        if ($error !== '' && ((\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7)) || (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6))))
            echo Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы действительно хотите простить все ошибки в мероприятии?',
                    'method' => 'post',
                ],]);
        ?>
    </p>

    <div class="content-container" style="color: #ff0000; font: 18px bold;">
        <?php
        $error = $model->getErrorsWork();
        if ($error != '')
        {
            echo '<p style="">';
            echo $error;
            echo '</p>';
        }
        ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'start_date',
            'finish_date',
            ['attribute' => 'event_type_id', 'value' => $model->eventType->name],
            ['attribute' => 'event_form_id', 'value' => $model->eventForm->name],
            'eventWayString',
            'address',
            ['attribute' => 'event_level_id', 'value' => $model->eventLevel->name],
            ['attribute' => 'scopesString', 'format' => 'raw'],
            'participants_count',
            'childs',
            'teachers',
            'others',
            'leftAge',
            'rightAge',
            ['attribute' => 'is_federal', 'value' => function($model){
                if ($model->is_federal == 1)
                    return 'Да';
                else
                    return 'Нет';
            }],
            ['attribute' => 'responsible_id', 'value' => $model->responsible2_id !== null ? $model->responsibleWork->shortName.'<br>'.$model->responsibleWork2->shortName : $model->responsibleWork->shortName,
                'format' => 'raw'],
            ['attribute' => 'eventDepartment', 'label' => 'Мероприятие проводит', 'value' => function($model){
                $tech = \app\models\work\EventBranchWork::find()->where(['branch_id' => 2])->andWhere(['event_id' => $model->id])->all();
                $quant = \app\models\work\EventBranchWork::find()->where(['branch_id' => 1])->andWhere(['event_id' => $model->id])->all();
                $cdntt = \app\models\work\EventBranchWork::find()->where(['branch_id' => 3])->andWhere(['event_id' => $model->id])->all();
                $mobquant = \app\models\work\EventBranchWork::find()->where(['branch_id' => 4])->andWhere(['event_id' => $model->id])->all();
                $cod = \app\models\work\EventBranchWork::find()->where(['branch_id' => 7])->andWhere(['event_id' => $model->id])->all();

                $result = '';
                if (count($tech) > 0)
                    $result = $result.'Технопарк';
                if (count($quant) > 0)
                    if ($result == '')
                        $result = $result.'Кванториум';
                    else
                        $result = $result.'<br>Кванториум';
                if (count($cdntt) > 0)
                    if ($result == '')
                        $result = $result.'ЦДНТТ';
                    else
                        $result = $result.'<br>ЦДНТТ';
                if (count($mobquant) > 0)
                    if ($result == '')
                        $result = $result.'Мобильный кванториум';
                    else
                        $result = $result.'<br>Мобильный кванториум';

                if (count($cod) > 0)
                    if ($result == '')
                        $result = $result.'Центр одаренных детей';
                    else
                        $result = $result.'<br>Центр одаренных детей';

                return $result;
            }, 'format' => 'raw'],
            ['attribute' => 'contains_education', 'value' => function($model){
                if ($model->contains_education == 0)
                    return 'Не содержит образовательных программы';
                else
                    return 'Содержит образовательные программы';
            }],
            'key_words',
            'comment',
            ['attribute' => 'order_id', 'value' => Html::a($model->orderWork->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $model->order_id])),
                'format' => 'raw'],
            ['attribute' => 'regulation_id', 'value' => Html::a($model->regulation->name, \yii\helpers\Url::to(['regulation/view', 'id' => $model->regulation_id])),
                'format' => 'raw'],

            ['attribute' => 'eventsLink', 'label' => 'Отчетные мероприятия', 'value' => function($model){
                $events = \app\models\work\EventsLinkWork::find()->where(['event_id' => $model->id])->all();
                $result = '';
                foreach ($events as $event)
                    $result = $result.$event->eventExternal->name.'<br>';
                return $result;
            }, 'format' => 'raw'],
            ['label' => 'Протоколы мероприятия', 'attribute' => 'protocol', 'value' => function ($model) {
                $split = explode(" ", $model->protocol);
                $result = '';
                for ($i = 0; $i < count($split) - 1; $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => $split[$i], 'type' => 'protocol'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Фотоматериалы', 'attribute' => 'photoFiles', 'value' => function ($model) {
                $split = explode(" ", $model->photos);
                $result = '';
                for ($i = 0; $i < count($split) - 1; $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => $split[$i], 'type' => 'photos'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Явочные документы', 'attribute' => 'reporting_doc', 'value' => function ($model) {
                $split = explode(" ", $model->reporting_doc);
                $result = '';
                for ($i = 0; $i < count($split) - 1; $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => $split[$i], 'type' => 'reporting'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Другие файлы', 'attribute' => 'otherFiles', 'value' => function ($model) {
                $split = explode(" ", $model->other_files);
                $result = '';
                for ($i = 0; $i < count($split) - 1; $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['event/get-file', 'fileName' => $split[$i], 'type' => 'other'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['attribute' => 'linkGroups', 'format' => 'raw'],
            'creatorString',
            'editorString'
        ],
    ]) ?>

</div>
