<?php

namespace common\components\access;

use common\repositories\rac\UserPermissionFunctionRepository;
use frontend\models\work\rac\PermissionFunctionWork;
use Yii;

class AuthDataCache
{
    /*
     * Формат данных:
     * Redis SETS
     * key: value1, value2, ...
     * user_id: function_id_1, function_id_2, ...
     */

    const CACHE_LIFETIME = 28800;
    private UserPermissionFunctionRepository $userFunctionRepository;

    public function __construct(UserPermissionFunctionRepository $userFunctionRepository)
    {
        $this->userFunctionRepository = $userFunctionRepository;
    }

    public function loadDataFromDB($userId)
    {
        $functions = $this->userFunctionRepository->getPermissionsByUser($userId);
        $key = $this->getSetKey($userId);
        $transactionFlag = true;
        foreach ($functions as $function) {
            /** @var PermissionFunctionWork $function */
            if (Yii::$app->redis->executeCommand('SADD', [$key, $function->id, 'EX', self::CACHE_LIFETIME]) != 'OK') {
                $transactionFlag = false;
            }
        }

        if (!$transactionFlag) {
            Yii::$app->redis->executeCommand('DEL', $key);
        }
    }

    private function getSetKey($userId)
    {
        return "user:permissions:{$userId}";
    }
}