<?php

namespace common\components\traits;

use DateTime;
use InvalidArgumentException;

trait DataFormatTrait
{
    public static $Ymd_dash = 1;
    public static $Ymd_dot = 2;
    public static $dmY_dash = 3;
    public static $dmY_dot = 4;
    public static $dmy_dash = 5;
    public static $dmy_dot = 6;

    public static function getFormats()
    {
        return [
            self::$Ymd_dash => 'Y-m-d',
            self::$Ymd_dot => 'Y.m.d',
            self::$dmY_dash => 'd-m-Y',
            self::$dmY_dot => 'd.m.Y',
            self::$dmy_dash => 'd-m-y',
            self::$dmy_dot => 'd.m.y',
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

    public function splitDates($dates)
    {
        $pairDates = explode(' - ', $dates);
        if (count($pairDates) != 2) {
            throw new InvalidArgumentException('Некорректный формат дат');
        }

        return $pairDates;
    }

    public function format($data, $baseType, $targetType)
    {
        $datetime = DateTime::createFromFormat(self::get($baseType), $data);
        return $datetime->format(self::get($targetType));
    }
}