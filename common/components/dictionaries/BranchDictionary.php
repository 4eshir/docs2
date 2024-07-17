<?php

namespace common\components\dictionaries;

class BranchDictionary extends BaseDictionary
{
    const TECHNOPARK = 'techno';
    const QUANTORIUM = 'quant';
    const CDNTT = 'cdntt';
    const COD = 'cod';
    const MOBILE_QUANTUM = 'mob_quant';

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::TECHNOPARK => 'Технопарк',
            self::QUANTORIUM => 'Кванториум',
            self::CDNTT => 'ЦДНТТ',
            self::COD => 'ЦОД',
            self::MOBILE_QUANTUM => 'Мобильный Кванториум',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::TECHNOPARK],
            $this->list[self::QUANTORIUM],
            $this->list[self::CDNTT],
            $this->list[self::COD],
            $this->list[self::MOBILE_QUANTUM],
        ];
    }
}