<?php

use common\helpers\html\HtmlBuilder;
use common\models\scaffold\DocumentIn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentInWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */
/* @var $scanFile */
/* @var $docFiles */
/* @var $appFiles */

$this->title = 'Входящий документ №' . $model->fullNumber;
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';

$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="document-in-create">

    <?php if (!Yii::$app->redis->isConnected()): ?>
        <?= HtmlBuilder::createWarningMessage(
            'Внимание!',
            'Отключена система блокировки ресурсов. Будьте оперативны при заполнении карточек и не оставляйте надолго открытой форму редактирования'
        ) ?>
    <?php else: ?>
        <?= HtmlBuilder::createInfoMessage(
            'Работает система блокировки ресурсов. Данный ресурс сейчас заблокирован для других пользователей, пока Вы не завершите редактирование<hr>
                            <i>Вас автоматически перенаправит на страницу просмотра после <b>10 минут бездействия</b>. Внесенные изменения не будут применены. Будьте внимательны!</i>'
        ) ?>
    <?php endif; ?>

    <h3><?= Html::encode($this->title) ?></h3>

    <br>

    <?= $this->render('_form', [
        'model' => $model,
        'correspondentList' => $correspondentList,
        'availablePositions' => $availablePositions,
        'availableCompanies' => $availableCompanies,
        'mainCompanyWorkers' => $mainCompanyWorkers,
        'scanFile' => $scanFile,
        'docFiles' => $docFiles,
        'appFiles' => $appFiles,
    ]) ?>

</div>

<script>
    window.onload = function() {
        initObjectData(<?= $model->id ?>, '<?= DocumentIn::tableName() ?>', 'index.php?r=document/document-in/view&id=<?= $model->id ?>');
    }

    const intervalId = setInterval(() => {
        refreshLock();
    }, 600000);
</script>
