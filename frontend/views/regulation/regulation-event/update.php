<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model RegulationWork */
/* @var $scanFile */

$this->title = 'Редактировать положение о мероприятии: ' . $model->name;

$this->params['breadcrumbs'][] = ['label' => Yii::$app->regulationType->get(RegulationTypeDictionary::TYPE_EVENT), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="regulation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'scanFile' => $scanFile,
    ]) ?>

</div>
