<?php

namespace common\components\dictionaries\base;


class StudyStatusDictionary extends BaseDictionary
{
    const INACTIVE = 0;
    const ACTIVE = 1;
    const DEDUCT = 2;
    const TRANSFER_IN = 3;
    const TRANSFER_OUT = 4;
    const ERROR = 5;
    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::INACTIVE => 'Не состоит в группе',
            self::ACTIVE => 'Зачислен в группу',
            self::DEDUCT => 'Отчислен из группы',
            self::TRANSFER_IN => 'Переведён в группу',
            self::TRANSFER_OUT => 'Переведён из группу',
            self::ERROR => 'Ошибка статуса'
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::INACTIVE],
            $this->list[self::ACTIVE],
            $this->list[self::DEDUCT],
            $this->list[self::TRANSFER_IN],
            $this->list[self::TRANSFER_OUT],
            $this->list[self::ERROR]
        ];
    }
}