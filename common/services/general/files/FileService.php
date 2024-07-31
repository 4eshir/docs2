<?php

namespace common\services\general\files;

use common\helpers\files\filenames\DocumentInFileNameGenerator;
use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\models\work\general\FilesWork;
use common\services\general\files\download\FileDownloadServer;
use common\services\general\files\download\FileDownloadYandexDisk;
use DomainException;
use frontend\events\general\FileCreateEvent;
use frontend\events\general\FileDeleteEvent;

class FileService
{
    public DocumentInFileNameGenerator $filenameGenerator;

    public function __construct(DocumentInFileNameGenerator $filenameGenerator)
    {
        $this->filenameGenerator = $filenameGenerator;
    }

    public function downloadFile($filepath)
    {
        $downloadServ = new FileDownloadServer($filepath);
        $downloadYadi = new FileDownloadYandexDisk($filepath);

        $type = FilesHelper::FILE_SERVER;
        $downloadServ->LoadFile();

        if (!$downloadServ->success) {
            $downloadYadi->LoadFile();
            $type = FilesHelper::FILE_YADI;

            if (!$downloadYadi->success) {
                throw new \Exception('File not found');
            }
        }

        return [
            'type' => $type,
            'obj' => $type == FilesHelper::FILE_SERVER ?
                $downloadServ :
                $downloadYadi
        ];
    }

    public function uploadFile($model, $file, $filetype, $loadtype, $basePath, $params = [])
    {
        // тут будет стратегия для загрузки на яндекс диск... потом

        $filepath = $basePath . $this->filenameGenerator->generateFileName($model, $filetype, $params);
        $model->recordEvent(
            new FileCreateEvent(
                $model::tableName(),
                $model->id,
                $filetype,
                StringFormatter::removeUntilFirstSlash($filepath),
                $loadtype
            ),
            get_class($model)
        );

        if ($file) {
            $file->saveAs($filepath);
        }
    }

    public function deleteFile($fileId)
    {
        $entity = FilesWork::find()->where(['id' => $fileId])->one();
        /** @var FilesWork $entity */
        $entity->recordEvent(new FileDeleteEvent($entity->filepath), get_class($entity));
        if ($entity->delete()) {
            $entity->releaseEvents();
        }
        else {
            throw new DomainException('Произошла ошибка при удалении записи из таблицы files');
        }
    }
}