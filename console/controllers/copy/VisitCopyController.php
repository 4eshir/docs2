<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use Yii;
use yii\console\Controller;

class VisitCopyController extends Controller
{
    public function actionCopyVisit(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM visit");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('visit',
                [
                    'id' => $record['id'],
                    'lessons' => NULL,
                    'training_group_participant_id' => $record['training_group_participant_id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionDeleteVisit()
    {
        Yii::$app->db->createCommand()->delete('visit')->execute();
    }
    public function actionDeleteAll()
    {
        $this->actionDeleteVisit();
    }
    public function actionCopyAll(){
        $this->actionCopyVisit();
    }
}