<?php

namespace console\controllers\copy;

use common\services\general\PeopleStampService;
use console\helper\FileTransferHelper;
use frontend\models\work\order\DocumentOrderWork;
use Yii;
use yii\console\Controller;

class DocumentOrderCopyController extends Controller
{
    private PeopleStampService $peopleStampService;
    private FileTransferHelper $fileTransferHelper;
    public function __construct(
        $id,
        $module,
        PeopleStampService $peopleStampService,
        FileTransferHelper $fileTransferHelper,
        $config = [])
    {
        $this->peopleStampService = $peopleStampService;
        $this->fileTransferHelper = $fileTransferHelper;
        parent::__construct($id, $module, $config);
    }

    public function actionOrderCopy(){
        $query = Yii::$app->old_db->createCommand("SELECT * FROM document_order");
        $command = Yii::$app->db->createCommand();
        foreach ($query->queryAll() as $record) {
            $command->insert('document_order',
                [
                    'id' => $record['id'],
                    'order_copy_id' => $record['order_copy_id'],
                    'order_number' => $record['order_number'],
                    'order_postfix' => $record['order_postfix'],
                    'order_name' => $record['order_name'],
                    'order_date' => $record['order_date'],
                    'signed_id' => $record['signed_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['signed_id']) : NULL,
                    'bring_id' => $record['bring_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['bring_id']) : NULL,
                    'executor_id' => $record['executor_id'] != '' ? $this->peopleStampService->createStampFromPeople($record['executor_id']) : NULL,
                    'key_words' => $record['key_words'],
                    'creator_id' => $record['creator_id'],
                    'last_edit_id' => $record['last_edit_id'],
                    'type' => $record['type'],
                    'state' => $record['state'],
                    'nomenclature_id' => $record['nomenclature_id'],
                    'study_type' => $record['study_type'],
                ]
            );
            $this->fileTransferHelper->createFiles(
                [
                    'scan' => $record['scan'],
                    'doc' => $record['doc'],
                    'app' => NULL,
                    'table' => DocumentOrderWork::tableName(),
                    'row' => $record['id'],
                ]
            );
            $command->execute();
        }
    }
    public function actionCopyAll()
    {
        $this->actionOrderCopy();
    }
}