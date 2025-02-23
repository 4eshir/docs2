<?php
namespace console\helper;
use Yii;

class FileTransferHelper
{
    public function createFiles($data){
        $scan = $data['scan'];
        $doc = $data['doc'];
        $app = $data['app'];
        $table = $data['table'];
        $row  = $data['row'];
        if($scan != null){
            $command = Yii::$app->db->createCommand();
            $command->insert('files',
                [
                    'table_name' => $table,
                    'table_row_id' => $row,
                    'file_type' => 'scan',
                    'filepath' => $scan
                ]
            );
            $command->execute();
        }
        if($doc != null){
            $command = Yii::$app->db->createCommand();
            $command->insert('files',
                [
                    'table_name' => $table,
                    'table_row_id' => $row,
                    'file_type' => 'doc',
                    'filepath' => $doc
                ]
            );
            $command->execute();
        }
        if($app != null){
            $command = Yii::$app->db->createCommand();
            $command->insert('files',
                [
                    'table_name' => $table,
                    'table_row_id' => $row,
                    'file_type' => 'app',
                    'filepath' => $app
                ]
            );
            $command->execute();
        }
    }
}