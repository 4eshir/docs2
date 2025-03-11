<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class ParticipantAchievementCopyController extends Controller
{
    public function actionCopyParticipantAchievement(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM participant_achievement");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('participant_achievement',
                [
                    //??????
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteParticipantAchievement(){
        Yii::$app->db->createCommand()->delete('participant_achievement')->execute();
    }
    public function actionDeleteAll(){
        $this->actionCopyParticipantAchievement();
    }
    public function actionCopyAll(){
        $this->actionCopyParticipantAchievement();
    }
}