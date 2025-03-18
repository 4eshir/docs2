<?php

namespace common\helpers\files;

class FilePaths
{
    public const BASE_FILEPATH = '/upload/files';
    public const TEMPLATE_FILEPATH = '/upload/templates';
    public const TEMP_FILEPATH = self::BASE_FILEPATH . '/temp';
    public const EXAMPLE_FILEPATH = '/upload/example';
    public const EXAMPLE_UTP = self::EXAMPLE_FILEPATH . '/utp-example.xlsx';

    public const FILE_DOWNLOAD_SVG = 'svg/download-file.svg';
    public const FILE_NO_DOWNLOAD_SVG = 'svg/no-download-file.svg';
    public const INFO_SVG = 'svg/information-circle.svg';
    public const PERSONAL_DATE_SVG = 'svg/personal-data.svg';


    public const CERTIFICATE_TEMPLATES = self::BASE_FILEPATH . '/certificate-templates/';
    public const REPORT_TEMPLATES = self::TEMPLATE_FILEPATH . '/report-templates/';
}