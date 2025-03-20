<?php

namespace console\controllers\seeders;

use common\repositories\educational\VisitRepository;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use Yii;
use yii\console\Controller;

class VisitSeederController extends Controller
{
    private RandomHelper $randomHelper;
    private VisitRepository $visitRepository;
    public function __construct(
        $id,
        $module,
        RandomHelper $randomHelper,
        VisitRepository $visitRepository,
        $config = []
    )
    {
        $this->randomHelper = $randomHelper;
        $this->visitRepository = $visitRepository;
        parent::__construct($id, $module, $config);
    }
    public function actionRun($amount = 10, $amountVisit = 5){
        $index = 0;
        while ($index < $amount){
            $amountVisits = rand(1, $amountVisit);
            $lessons = [];
            for($counter = 0; $counter < $amountVisits; $counter++){
                $lessons[] = '{"lesson_id":' . $this->randomHelper->randomItem(TrainingGroupParticipantWork::find()->all())['id'] . ',' . '"status":' . rand(0,3). '}';
            }
            $lessons = '['.(implode(',', $lessons)).']';
            $command = Yii::$app->db->createCommand();
            $trainingGroupParticipantId = $this->randomHelper->randomItem(TrainingGroupParticipantWork::find()->all())['id'];
            if (!$this->visitRepository->getByTrainingGroupParticipant($trainingGroupParticipantId)) {
                $command->insert('visit', [
                    'lessons' => $lessons,
                    'training_group_participant_id' => $trainingGroupParticipantId
                ]);
                $command->execute();
                $index++;
            }
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('visit')->execute();
    }
}