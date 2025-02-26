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
    public function actionActCopy()
    {
        $query = Yii::$app->old_db->createCommand("SELECT * FROM team_name");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $teamNameId  = $record['id'];
            $acts = Yii::$app->old_db->createCommand("SELECT * FROM team WHERE team_name_id = $teamNameId")->queryAll();
            if (count($acts) > 0) {
                $act = $acts[0];
                $teacherParticipantId = $act['teacher_participant_id'];
                $teacherParticipants = Yii::$app->old_db->createCommand("SELECT * FROM teacher_participant WHERE id = $teacherParticipantId")->queryAll();;
                //создание ActParticipant через save() для получения id при заполнениии squad_participant
                $command->insert('act_participant',
                    [
                        'teacher_id' => $teacherParticipants[0]['teacher_id'],
                        'teacher2_id' => $teacherParticipants[0]['teacher2_id'],
                        //'branch' => $teacherParticipants[0]['branch_id'],
                        'focus' => $teacherParticipants[0]['focus'],
                        'type' => $teacherParticipants[0]['teacher_id'],
                        'nomination' => $teacherParticipants[0]['nomination'],
                        'team_name_id' => $teamNameId,
                        'form' => $teacherParticipants[0]['allow_remote_id'],
                        'foreign_event_id' => $record['foreign_event_id'],
                        'allow_remote' => $teacherParticipants[0]['allow_remote_id'],
                    ]
                );
                //$command->execute();
            }
        }
    }
    //document_order_supplement
    public function actionCopyAll(){
        $this->actionTeamNameCopy();
    }
}