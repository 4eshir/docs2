<?php

namespace common\components\dictionaries\base;

class DocumentStatusDictionary extends BaseDictionary
{
    const ARCHIVE = 1;
    const EXPIRED = 2;
    const NEEDANSWER = 3;
    const CURRENT = 4;
    const RESERVED = 5;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::ARCHIVE => 'Архивные',
            self::EXPIRED => 'Просроченные',
            self::NEEDANSWER => 'Требуют ответа',
            self::CURRENT => 'Актуальные',
            self::RESERVED => 'Резерные',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::ARCHIVE],
            $this->list[self::EXPIRED],
            $this->list[self::NEEDANSWER],
            $this->list[self::CURRENT],
            $this->list[self::RESERVED],
        ];
    }
}