<?php

namespace common\dictionaries;

abstract class BaseDictionary
{
    protected $list;

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }

    /**
     * Кастомная сортировка объектов в $list
     * @return mixed
     */
    abstract public function customSort();
}