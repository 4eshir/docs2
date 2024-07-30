<?php

namespace common\helpers\files\filenames;

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\work\document_in_out\DocumentInWork;
use DomainException;
use InvalidArgumentException;
use ZipStream\File;

class DocumentInFileNameGenerator implements FileNameGeneratorInterface
{
    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_SCAN:
                return $this->generateScanFileName($object, $params);
            case FilesHelper::TYPE_DOC:
                return $this->generateDocFileName($object, $params);
            case FilesHelper::TYPE_APP:
                return $this->generateAppFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }

    private function generateDocFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var DocumentInWork $object */
        $date = $object->local_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        if ($object->companyWork->short_name !== '') {
            $filename = 'Ред'.$params["counter"].'_Вх.'.$new_date.'_'.$object->local_number.'_'.$object->companyWork->short_name.'_'.$object->document_theme;
        }
        else {
            $filename = 'Ред'.$params["counter"].'_Вх.'.$new_date.'_'.$object->local_number.'_'.$object->companyWork->name.'_'.$object->document_theme;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->docFiles[$params['counter'] - 1]->extension;
    }

    private function generateScanFileName($object, $params = [])
    {
        /** @var DocumentInWork $object */
        $date = $object->local_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        if ($object->companyWork->short_name !== '') {
            $filename = 'Вх.'.$new_date.'_'.$object->local_number.'_'.$object->companyWork->short_name.'_'.$object->document_theme;
        }
        else {
            $filename = 'Вх.'.$new_date.'_'.$object->local_number.'_'.$object->companyWork->name.'_'.$object->document_theme;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->scanFile->extension;
    }

    private function generateAppFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var DocumentInWork $object */
        $date = $object->local_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);

        if ($object->company->short_name !== '') {
            $filename = 'Приложение'.$params["counter"].'_Вх.'.$new_date.'_'.$object->local_number.'_'.$object->companyWork->short_name.'_'.$object->document_theme;
        }
        else {
            $filename = 'Приложение'.$params["counter"].'_Вх.'.$new_date.'_'.$object->local_number.'_'.$object->companyWork->name.'_'.$object->document_theme;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->appFiles[$params['counter'] - 1]->extension;
    }
}