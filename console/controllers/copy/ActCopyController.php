<?php

namespace console\controllers\copy;

use Yii;
use yii\console\Controller;

class ActCopyController extends Controller
{
    public function actionTeamNameCopy(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM team_name");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('team_name', $record);
            $command->execute();
        }
    }
    //acts

    public function actionCopyAll(){
        $this->actionTeamNameCopy();
    }
}