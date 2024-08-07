<?php

namespace frontend\helpers;

class HeaderWizard
{
    public static function setFileHeaders($filename, $filesize)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
    }
}