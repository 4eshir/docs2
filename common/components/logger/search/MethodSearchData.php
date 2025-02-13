<?php

namespace common\components\logger\search;

use common\repositories\log\LogRepository;
use yii\db\ActiveQuery;

class MethodSearchData implements SearchDataInterface
{
    /**
     * @var string[] $controllerNames
     * @var string[] $actionNames
     * @var int[] $callTypes
     */
    public array $controllerNames;
    public array $actionNames;
    public array $callTypes;

    public function __construct(
        array $controllerNames = [],
        array $actionNames = [],
        array $callTypes = []
    )
    {
        $this->controllerNames = $controllerNames;
        $this->actionNames = $actionNames;
        $this->callTypes = $callTypes;
    }

    /**
     * Проверяет, есть ли в данных в виде json строки совпадающие key-value значения
     * Пример:
     *   addData {controllerName: 'test', callType: 12}
     *   $this->controllerNames = ['some', 'test']
     *   return - true
     *
     * @param string $addData
     * @return bool
     */
    public function haveData(string $addData) : bool
    {
        // Парсим JSON строку
        $data = json_decode($addData, true);

        // Проверяем, корректно ли распарсили JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false; // Возвращаем false, если есть ошибка парсинга
        }

        // Сопоставление ключей с массивами
        $checks = [
            'controllerName' => $this->controllerNames,
            'actionName' => $this->actionNames,
            'callType' => $this->callTypes,
        ];

        foreach ($checks as $key => $values) {
            if (isset($data[$key]) && in_array($data[$key], $values, true)) {
                return true;
            }
        }

        return false;
    }
}