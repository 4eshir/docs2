<?php

namespace common\helpers\files;

use frontend\models\work\general\FilesWork;

class FilesHelper
{
    const TYPE_SCAN = 'scan';
    const TYPE_DOC = 'doc';
    const TYPE_APP = 'app';

    const FILE_SERVER = 'server';
    const FILE_YADI = 'yadi';

    const LOAD_TYPE_SINGLE = 'single'; /** тип загрузки "единичный". перезаписывает существующую строку в БД при наличии @see FilesWork */
    const LOAD_TYPE_MULTI = 'multi'; /** тип загрузки "мульти". создает новые записи в @see FilesWork вне зависимости от существующих аналогичных строк*/

    public static function getFileTypes()
    {
        return [
            self::TYPE_SCAN => 'Сканы документов',
            self::TYPE_DOC => 'Редактируемые файлы документов',
            self::TYPE_APP => 'Приложения к документам'
        ];
    }

    public static function getFileType($index)
    {
        return self::getFileTypes()[$index];
    }

    public static function getFilenameFromPath($filepath)
    {
        $parts = explode('/', $filepath);
        return end($parts);
    }

    /**
     * Создает относительный путь к файлу на основе
     * @param string $tableName имени таблицы
     * @param string $fileType типа файла
     * @return string
     */
    public static function createAdditionalPath(string $tableName, string $fileType)
    {
        return FilePaths::BASE_FILEPATH . '/' . $tableName . '/' . $fileType . '/';
    }
}