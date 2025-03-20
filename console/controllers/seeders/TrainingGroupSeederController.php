<?php

namespace console\controllers\seeders;

use common\models\scaffold\TrainingGroup;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use Yii;
use yii\console\Controller;

class TrainingGroupSeederController extends Controller
{
    private RandomHelper $randomHelper;
    public function __construct(
        $id,
        $module,
        RandomHelper $randomHelper,
        $config = []
    )
    {
        $this->randomHelper = $randomHelper;
        parent::__construct($id, $module, $config);
    }
    public function actionRun($amount = 10){
        for($i = 0; $i < $amount; $i++){
            $command = Yii::$app->db->createCommand();
            $command->insert('training_group', [
                'number' =>
                'training_program_id' =>
                'start_date' =>
                'finish_date' =>
                'open' =>
                'budget' =>
                'branch' =>
                'order_stop' =>
                'archive' =>
                'protection_confirm' =>
                'is_network' =>
                'state' =>
                'creator_id' =>
                'last_edit_id' =>
            ]);
            $command->execute();
        }
    }
    public function actionDelete(){
        Yii::$app->db->createCommand()->delete('training_group')->execute();
    }
}