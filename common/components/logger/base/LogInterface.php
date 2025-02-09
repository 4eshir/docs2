<?php


use common\models\work\LogWork;

interface LogInterface
{
    const LVL_INFO = 0;
    const LVL_WARNING = 1;
    const LVL_ERROR = 2;

    const TYPE_DEFAULT = 0;
    const TYPE_METHOD = 1;
    const TYPE_CRUD = 2;

    public function write(LogWork $log) : bool;
    public function read(SearchLogData $searchData) : LogInterface;
}