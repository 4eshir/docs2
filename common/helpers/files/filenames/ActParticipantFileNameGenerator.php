<?php

namespace common\helpers\files\filenames;

use app\models\work\event\ForeignEventWork;
use app\models\work\team\ActParticipantWork;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\repositories\general\FilesRepository;
use DomainException;
use frontend\models\work\general\FilesWork;
use InvalidArgumentException;

class ActParticipantFileNameGenerator implements FileNameGeneratorInterface
{
    private FilesRepository $filesRepository;
    public function __construct(FilesRepository $filesRepository)
    {
        $this->filesRepository = $filesRepository;
    }
    public function getOrdinalFileNumber($object, $fileType)
    {
        switch ($fileType) {
            case FilesHelper::TYPE_DOC:
                return $this->getOrdinalFileNumberDoc($object);
            case FilesHelper::TYPE_APP:
                return $this->getOrdinalFileNumberApp($object);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }
    private function getOrdinalFileNumberDoc($object)
    {
        $lastDocFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_DOC);
        /** @var FilesWork $lastDocFile */
        if ($lastDocFile) {
            preg_match('/Акт(\d+)_/', basename($lastDocFile->filepath), $matches);
            return (int)$matches[1];
        }
        return 0;
    }
    private function getOrdinalFileNumberApp($object)
    {
        $lastAppFile = $this->filesRepository->getLastFile($object::tableName(), $object->id, FilesHelper::TYPE_APP);
        /** @var FilesWork $lastAppFile */
        if ($lastAppFile) {
            preg_match('/Акт(\d+)_/', basename($lastAppFile->filepath), $matches);
            return (int)$matches[1];
        }
        return 0;
    }
    public function generateFileName($object, $fileType, $params = []): string
    {
        switch ($fileType) {
            case FilesHelper::TYPE_DOC:
                return $this->generateActFileName($object, $params);
            default:
                throw new InvalidArgumentException('Неизвестный тип файла');
        }
    }
    private function generateActFileName($object, $params = [])
    {
        $actFiles = $params['extensions'];
        if (!array_key_exists('counter', $params)) {
            throw new DomainException('Параметр \'counter\' обязателен');
        }
        /** @var ActParticipantWork $object */
        $number = $object->id;
        $filename =
            'Акт'.($this->getOrdinalFileNumber($object, FilesHelper::TYPE_DOC) + $params['counter']).
            '_Акт.'.$number;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = StringFormatter::CutFilename($res);
        return $res . '.' . $actFiles->extension;
    }
}