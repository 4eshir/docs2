<?php


/* @var $this yii\web\View */
?>
<?= $this->render('_groups_grid', [
    'dataProvider' => $dataProviderGroup,
    'model' => $model,
]);
?>
<?= $this->render('_group-participant_grid', [
    'dataProvider' => $dataProviderParticipant,
    'model' => $model,
    'nomenclature' => $nomenclature,
    'transferGroups' => $transferGroups,
]);
?>