<?php

namespace common\models;

use InvalidArgumentException;

class Error
{
    /**
     * ТИПЫ ОШИБОК
     * TYPE_BASE - ошибка, которая может быть только базовой.
     * TYPE_CRITICAL - ошибка, которая может быть только критической.
     * TYPE_CHANGEABLE - ошибка, которая может быть как критической, так и базовой, в зависимости от условий.
     */
    const TYPE_BASE = 0;
    const TYPE_CRITICAL = 1;
    const TYPE_CHANGEABLE = 2;

    const TYPES = [self::TYPE_BASE, self::TYPE_CRITICAL, self::TYPE_CHANGEABLE];

    private string $code;
    private string $description;
    private int $type;

    /**
     * Функции для изменения состояния ошибок
     * @var callable $makeFunction функция с условием создания ошибки
     * @var callable $fixFunction функция с условием удаления (исправления) ошибки
     * @var ?callable $changeTypeFunction функция с условием изменения состояния ошибки (только для {@see self::TYPE_CHANGEABLE})
     */
    private $makeFunction;
    private $fixFunction;
    private $changeStateFunction;

    public function __construct(
        string $code,
        string $description,
        int $type,
        callable $makeFunction,
        callable $fixFunction,
        callable $changeStateFunction = null
    )
    {
        if (!in_array($type, self::TYPES)) {
            throw new InvalidArgumentException('Неизвестный тип ошибки');
        }

        $this->code = $code;
        $this->description = $description;
        $this->type = $type;
        $this->makeFunction = $makeFunction;
        $this->fixFunction = $fixFunction;
        $this->changeStateFunction = $changeStateFunction;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMakeFunction()
    {
        return $this->makeFunction;
    }

    public function getFixFunction()
    {
        return $this->fixFunction;
    }

    public function getChangeStateFunction()
    {
        return $this->changeStateFunction;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function setType(int $type)
    {
        $this->type = $type;
    }

    public function setMakeFunction(callable $makeFunction)
    {
        $this->makeFunction = $makeFunction;
    }

    public function setFixFunction(callable $fixFunction)
    {
        $this->fixFunction = $fixFunction;
    }

    public function setChangeStateFunction(callable $changeStateFunction)
    {
        $this->changeStateFunction = $changeStateFunction;
    }

    public function makeError($rowId)
    {
        return ($this->makeFunction)($rowId);
    }

    public function fixError($errorId)
    {
        return ($this->fixFunction)($errorId);
    }

    public function changeState(...$args)
    {
        return call_user_func_array($this->changeStateFunction, $args);
    }
}