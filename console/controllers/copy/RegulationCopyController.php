<?php

namespace console\controllers\copy;

use console\helper\FileTransferHelper;
use frontend\models\work\regulation\RegulationWork;
use Yii;
use yii\console\Controller;

class RegulationCopyController extends Controller
{
    private FileTransferHelper $fileTransferHelper;
    public function __construct(
        $id,
        $module,
        FileTransferHelper $fileTransferHelper,
        $config = []
    )
    {
        $this->fileTransferHelper = $fileTransferHelper;
        parent::__construct($id, $module, $config);
    }
    public function actionCopyRegulation(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM regulation");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('regulation',
                [
                    'id' => $record['id'],
                    'date' => $record['date'],
                    'name' => $record['name'],
                    'order_id' => $record['order_id'],
                    'short_name' => $record['short_name'],
                    'ped_council_date' => $record['ped_council_date'],
                    'ped_council_number' => $record['ped_council_number'],
                    'par_council_date' => $record['par_council_date'],
                    'state' => $record['state'],
                    'regulation_type' => $record['regulation_type_id'],
                ]
            );
            $this->fileTransferHelper->createFiles(
                [
                    'scan' => $record['scan'],
                    'doc' => $record['doc'],
                    'app' => $record['app'],
                    'table' => RegulationWork::tableName(),
                    'row' => $record['id'],
                ]
            );
            $command->execute();
        }
    }
}