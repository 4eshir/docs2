<?php

namespace common\helpers;

use yii\helpers\Html;

class StringFormatter
{
    const FORMAT_RAW = 1;
    const FORMAT_LINK = 2;

    public static function getFormats()
    {
        return [
            self::FORMAT_RAW => 'Обычная строка',
            self::FORMAT_LINK => 'Ссылка типа \<a\>',
        ];
    }

    public static function stringAsLink($name, $url)
    {
        return Html::a($name, $url);
    }

    public static function getFilenameFromPath($filepath)
    {
        $parts = explode('/', $filepath);
        return end($parts);
    }
}