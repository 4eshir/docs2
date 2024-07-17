<?php

namespace common\helpers;

class FilesHelper
{
    const TYPE_SCAN = 'scan';
    const TYPE_DOC = 'doc';
    const TYPE_APP = 'app';

    public static function getFileTypes()
    {
        return [
            self::TYPE_SCAN => 'Сканы документов',
            self::TYPE_DOC => 'Редактируемые файлы документов',
            self::TYPE_APP => 'Приложения к документам'
        ];
    }
}