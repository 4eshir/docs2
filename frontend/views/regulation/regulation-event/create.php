<?php

use common\components\dictionaries\base\RegulationTypeDictionary;
use frontend\models\work\regulation\RegulationWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model RegulationWork */

$this->title = 'Добавить положение о мероприятии';
$this->params['breadcrumbs'][] = ['label' => Yii::$app->regulationType->get(RegulationTypeDictionary::TYPE_EVENT), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regulation-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
