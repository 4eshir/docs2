<?php


namespace app\models\strategies\FileUploadStrategy;

use app\models\components\YandexDiskContext;
use app\models\strategies\FileUploadStrategy\AbstractFileUpload;
use yii\db\ActiveRecord;

class FileUploadYandexDisk extends AbstractFileUpload
{
    const ADDITIONAL_PATH = 'DSSD'; //дополнительный путь к папке на яндекс диске

    function __construct($tFilepath, $tFilename)
    {
        $this->filepath = $tFilepath;
        $this->filename = $tFilename;
    }
    
    public function LoadFile()
    {
        //nothing
    }
}