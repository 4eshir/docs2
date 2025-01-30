<?php

use yii\helpers\Html;

/* @var $model \app\models\work\order\OrderMainWork */
/* @var $people */
/* @var $users */
/* @var $modelExpire */
/* @var $orders */
/* @var $regulations */
$this->title = 'Добавить приказ об основной деятельности';
$this->params['breadcrumbs'][] = ['label' => 'Приказы об осн. деятельности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="">

        <h3><?= Html::encode($this->title) ?></h3>
        <br>

        <?= $this->render('_form', [
            'model' => $model,
            'people' => $people,
            'users' => $users,
            'modelExpire' => $modelExpire,
            'orders' => $orders,
            'regulations' => $regulations
        ]) ?>

    </div>

