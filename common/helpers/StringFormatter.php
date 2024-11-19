<?php

namespace common\helpers;

use PhpOffice\PhpSpreadsheet\Reader\Xls\MD5;
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

    public static function CutFilename($filename, $maxlength = 200)
    {
        $result = '';
        $splitName = explode("_", $filename);
        $i = 0;
        while (strlen($result) < $maxlength - strlen($splitName[$i]) && $i < count($splitName)) {
            $result = $result."_".$splitName[$i];
            $i++;
        }

        return mb_substr($result, 1);
    }

    public static function removeUntilFirstSlash($string) {
        $firstSlashPosition = strpos($string, '/');

        if ($firstSlashPosition !== false) {
            return '/' . substr($string, $firstSlashPosition + 1);
        }

        return $string;
    }

    public static function getLastSegmentBySlash($string) {
        $lastSlashPos = strrpos($string, '/');

        if ($lastSlashPos !== false) {
            return substr($string, $lastSlashPos + 1);
        }

        return $string;
    }

    public static function getLastSegmentByBackslash($string) {
        $lastSlashPos = strrpos($string, '\\');

        if ($lastSlashPos !== false) {
            return substr($string, $lastSlashPos + 1);
        }

        return $string;
    }

    public static function createHash(string $str)
    {
        return MD5($str);
    }
}