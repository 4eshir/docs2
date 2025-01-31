<?php

namespace common\helpers\files;

class FilePaths
{
    public const BASE_FILEPATH = '/upload/files';
    public const TEMP_FILEPATH = self::BASE_FILEPATH . '/temp';
    public const EXAMPLE_FILEPATH = '/upload/example';
    public const EXAMPLE_UTP = self::EXAMPLE_FILEPATH . '/utp-example.xlsx';

    public const FILE_DOWNLOAD_SVG = 'svg/download-file.svg';
    public const FILE_NO_DOWNLOAD_SVG = 'svg/no-download-file.svg';
}