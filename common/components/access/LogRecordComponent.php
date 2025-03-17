<?php

namespace common\components\access;
use Yii;
class LogRecordComponent
{
    public const UNBLOCK = 0;
    public const BLOCK = 1;

    public function checkBlock($key)
    {
        return !Yii::$app->redis->executeCommand('EXISTS', [$key]);
    }
    public function block($key, $time)
    {
        Yii::$app->redis->executeCommand('SET', [$key,self::BLOCK]);
        Yii::$app->redis->executeCommand('EXPIRE', [$key, $time]);
    }
    public function unblock($key) {
        Yii::$app->redis->executeCommand('DEL', [$key]);
    }
}