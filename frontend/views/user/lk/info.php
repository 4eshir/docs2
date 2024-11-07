<?php

use frontend\models\work\general\UserWork;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model UserWork */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu', ['model' => $model]) ?>

    <div class="content-container" style="float: left">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'surname',
                'firstname',
                'patronymic',
                'username',
            ],
        ]) ?>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>