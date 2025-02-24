<?php

namespace frontend\helpers;

class HeaderWizard
{
    public static function setFileHeaders(string $filename, int $filesize)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
    }

    public static function setCsvLoadHeaders(string $filename)
    {
        header('Content-Type: text/csv;charset=windows-1251');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
    }
}