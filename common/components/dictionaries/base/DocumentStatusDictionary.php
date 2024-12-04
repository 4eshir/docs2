<?php

namespace common\components\dictionaries\base;

class DocumentStatusDictionary extends BaseDictionary
{
    const ARCHIVE = 0;
    const EXPIRED = 1;
    const NEEDANSWER = 2;
    const CURRENT = 3;
    const RESERVED = 4;

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