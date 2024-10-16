<?php

namespace common\components\dictionaries\base;
class NomenclatureDictionary extends BaseDictionary
{
    public const ORDER = '02-02';
    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::ORDER => '02-02'

        ];
    }
    public function customSort()
    {
        return [
            $this->list[self::ORDER],
        ];
    }
}