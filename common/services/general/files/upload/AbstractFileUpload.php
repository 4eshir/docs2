<?php


namespace app\models\strategies\FileUploadStrategy;


use yii\db\ActiveRecord;

abstract class AbstractFileUpload
{
    public $filename;
    public $filepath;

    public $success;
    //public $file;

    abstract public function LoadFile();
}