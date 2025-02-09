<?php


use common\models\work\LogWork;
use common\repositories\log\LogRepository;

class MethodLog implements LogInterface
{
    // Типы вызовов логируемого метода
    const CTYPE_ACTION = 0;
    const CTYPE_SYSTEM = 1;

    public string $controllerName;
    public string $actionName;
    public int $callType;

    // query-параметры url или параметры вызываемой функции
    public array $queryParams;

    private LogRepository $repository;

    public function __construct(LogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function write(LogWork $log): bool
    {
        if ($this->repository->save($log)) {
            return true;
        }

        return false;
    }

    public function read(SearchLogData $searchData): LogInterface
    {
        // TODO: Implement read() method.
    }
}