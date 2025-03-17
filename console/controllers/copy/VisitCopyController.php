<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use frontend\models\work\educational\journal\VisitLesson;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class VisitCopyController extends Controller
{
    public function actionCopyVisitWithoutParticipant(){
        $visits = Yii::$app->old_db->createCommand("SELECT * FROM visit WHERE training_group_participant_id IS NULL AND id < 1700000")->queryAll();
        var_dump(count($visits));
        foreach ($visits as $visit){
            $foreignEventParticipantId = $visit['foreign_event_participant_id'];
            $lessonId = $visit['training_group_lesson_id'];
            $participantId = (Yii::$app->old_db->createCommand("SELECT * FROM foreign_event_participants WHERE id = $foreignEventParticipantId")->queryOne())['id'];
            $lesson = Yii::$app->old_db->createCommand("SELECT * FROM training_group_lesson WHERE id = $lessonId")->queryOne();
            $trainingGroupId = $lesson['training_group_id'];
            $trainingGroupParticipantId = (Yii::$app->old_db->createCommand("SELECT * FROM training_group_participant WHERE training_group_id = $trainingGroupId AND participant_id = $participantId")->queryOne())['id'];
            var_dump($trainingGroupParticipantId);
            /*$trainingGroupParticipantId = Yii::$app->db->createCommand()->update('visit', [
                'training_group_participant_id' => $trainingGroupParticipantId,
            ] , ['id' => $visit['id']])->execute();*/
        }
    }
    public function actionCopyVisit(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM training_group_participant");
        foreach ($query->queryAll() as $participant) {
            $lessons = [];
            $participantId = $participant['id'];
            $visits =  Yii::$app->old_db->createCommand("SELECT * FROM visit WHERE training_group_participant_id = $participantId")->queryAll();
            foreach ($visits as $visit) {
                $status = $visit['status'];
                $lessonId = $visit['training_group_lesson_id'];
                $lessons[] = "{\"lesson_id\":$lessonId,\"status\":$status}";
            }
            if (count($visits) > 0) {
                Yii::$app->db->createCommand()->insert('visit', [
                    'lessons' => json_encode($lessons),
                    'training_group_participant_id' => $participantId,
                ])->execute();
            }
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
       // $this->actionCopyVisitWithoutParticipant();
    }
}