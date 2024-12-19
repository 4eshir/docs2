<?php

namespace common\helpers;

use DateTime;
use InvalidArgumentException;

class DateFormatter
{
    const Ymd_dash = 1;
    const Ymd_dot = 2;
    const dmY_dash = 3;
    const dmY_dot = 4;
    const dmy_dash = 5;
    const dmy_dot = 6;
    const mdY_slash = 7;
    const Ymd_without_separator = 10;
    const DEFAULT_YEAR_RANGE = '2018:2030';     // заданная по умолчанию ограничения по дате
    const DEFAULT_YEAR_START = '2018-01-01';    // заданная по умолчанию ограничение по начальной дате

    public static function getFormats()
    {
        return [
            self::Ymd_dash => 'Y-m-d',
            self::Ymd_dot => 'Y.m.d',
            self::dmY_dash => 'd-m-Y',
            self::dmY_dot => 'd.m.Y',
            self::dmy_dash => 'd-m-y',
            self::dmy_dot => 'd.m.y',
            self::mdY_slash => 'm/d/Y',
            self::Ymd_without_separator => 'Ymd',
        ];
    }

    public static function get($index)
    {
        $formats = self::getFormats();
        if (!array_key_exists($index, $formats)) {
            throw new InvalidArgumentException('Неизвестный формат даты');
        }

        return $formats[$index];
    }

    public static function splitDates($dates)
    {
        $pairDates = explode(' - ', $dates);
        if (count($pairDates) != 2) {
            throw new InvalidArgumentException('Некорректный формат дат');
        }

        return $pairDates;
    }

    public static function format($data, $baseType, $targetType)
    {
        $datetime = DateTime::createFromFormat(self::get($baseType), $data);
        return $datetime ? $datetime->format(self::get($targetType)) : $data;
    }
}