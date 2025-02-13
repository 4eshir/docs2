<?php

namespace common\components\logger;

use common\components\logger\base\BaseLog;
use common\components\logger\base\LogInterface;
use common\components\logger\method\MethodLog;

class LogFactory
{
    public static function createBaseLog(
        string $datetime,
        int $level,
        int $userId,
        string $text
    )
    {
        $log = new BaseLog(
            $datetime,
            $level,
            LogInterface::TYPE_DEFAULT,
            $userId,
            $text
        );

        return $log->write();
    }

    public static function createMethodLog(
        string $datetime,
        int $level,
        int $userId,
        string $text,
        string $controllerName,
        string $actionName,
        int $callType
    )
    {
        $log = new MethodLog(
            $datetime,
            $level,
            LogInterface::TYPE_METHOD,
            $userId,
            $text,
            $controllerName,
            $actionName,
            $callType
        );

        return $log->write();
    }
}