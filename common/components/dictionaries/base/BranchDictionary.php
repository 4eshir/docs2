<?php

namespace common\components\dictionaries\base;

class BranchDictionary extends BaseDictionary
{
    const TECHNOPARK = 1;
    const QUANTORIUM = 2;
    const CDNTT = 3;
    const COD = 4;
    const MOBILE_QUANTUM = 5;
    const PLANETARIUM = 6;
    const ADMINISTRATION = 7;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::TECHNOPARK => 'Технопарк',
            self::QUANTORIUM => 'Кванториум',
            self::CDNTT => 'ЦДНТТ',
            self::COD => 'ЦОД',
            self::MOBILE_QUANTUM => 'Мобильный Кванториум',
            self::PLANETARIUM => 'Планетарий',
            self::ADMINISTRATION => 'Администрация',
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
            $this->list[self::PLANETARIUM],
            $this->list[self::ADMINISTRATION],
        ];
    }
}