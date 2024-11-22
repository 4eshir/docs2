<?php

namespace common\helpers\files\filenames;

use app\models\work\order\OrderMainWork;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;

class TrainingGroupFileNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;

    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }
    public function getOrdinalFileNumber($object, $fileType)
    {
        // ВСЕ ЭТИ ФУНКЦИИ - В РАБОТУ, ТАМ ЗАГЛУШКИ ПОКА
        switch ($fileType) {
            case FilesHelper::TYPE_PHOTO:
                return $this->getOrdinalFileNumberPhoto($object);
            case FilesHelper::TYPE_PRESENTATION:
                return $this->getOrdinalFileNumberPresentation($object);
            case FilesHelper::TYPE_WORK:
                return $this->getOrdinalFileNumberWork($object);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }
    private function getOrdinalFileNumberPhoto($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_DOC);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Ред(\d+)_/', basename($lastDocFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    private function getOrdinalFileNumberPresentation($object)
    {
        $lastAppFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_APP);
        /** @var FilesWork $lastAppFile */
        if ($lastAppFile) {
            preg_match('/Приложение(\d+)_/', basename($lastAppFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    private function getOrdinalFileNumberWork($object)
    {
        $lastAppFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_APP);
        /** @var FilesWork $lastAppFile */
        if ($lastAppFile) {
            preg_match('/Приложение(\d+)_/', basename($lastAppFile->filepath), $matches);
            return (int)$matches[1];
        }

        return 0;
    }

    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_PHOTO:
                return $this->generatePhotoFileName($object, $params);
            case FilesHelper::TYPE_PRESENTATION:
                return $this->generatePresentationFileName($object, $params);
            case FilesHelper::TYPE_WORK:
                return $this->generateWorkFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }
    private function generatePhotoFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }
        /** @var OrderMainWork $object */
        $date = $object->order_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename =
                'Ред'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_DOC) + $params['counter']).
                '_Пр.'.$new_date.'_'.$object->order_number.'_'.'_'.$object->order_name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->docFiles[$params['counter'] - 1]->extension;
    }

    private function generatePresentationFileName($object, $params = [])
    {
        /** @var OrderMainWork $object */
        $date = $object->order_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'Пр.'.$new_date.'_'.$object->order_number.'_'.'_'.$object->order_name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->scanFile->extension;
    }

    private function generateWorkFileName($object, $params = [])
    {
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }

        /** @var OrderMainWork $object */
        $date = $object->order_date;
        $new_date = DateFormatter::format($date, DateFormatter::Ymd_dash, DateFormatter::Ymd_without_separator);
        $filename = 'Приложение'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_APP) +
                $params['counter']).'_Пр.'.$new_date.'_'.$object->order_number.'_'.'_'.$object->order_name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);

        return $res . '.' . $object->appFiles[$params['counter'] - 1]->extension;
    }







}