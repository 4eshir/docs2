<?php


class LogFactory
{
    public static function createBaseLog(
        string $datetime,
        int $level,
        int $type,
        int $userId,
        string $text
    )
    {

    }

    public static function createMethodLog(
        string $datetime,
        int $level,
        int $type,
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
            $type,
            $userId,
            $text,
            $controllerName,
            $actionName,
            $callType
        );

        $log->write();
    }
}