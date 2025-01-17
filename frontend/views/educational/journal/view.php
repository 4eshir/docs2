<?php

use frontend\forms\journal\JournalForm;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model JournalForm */

?>

<?= Html::a('Редактировать журнал', ['update', 'id' => $model->groupId], ['class' => 'btn btn-primary']) ?>
<div class="journal">
    <table class="table table-bordered">
        <?php foreach ($model->participantLessons as $participantLesson): ?>
            <tr>
                <td>
                    <?= $participantLesson->participant->getFIO(ForeignEventParticipantsWork::FIO_SURNAME_INITIALS); ?>
                </td>
                <?php foreach ($participantLesson->lessonIds as $lesson): ?>
                    <td>
                        <?= $lesson->getPrettyStatus() ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

</div>