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
use Yii;

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

    public function uploadFile($file, $filepath)
    {
        // тут будет стратегия для загрузки на яндекс диск... потом

        if ($file) {
            $file->saveAs($filepath);
        }
    }

    public function deleteFile($filepath)
    {
        // тут будет стратегия для загрузки на яндекс диск... потом

        if (file_exists(Yii::$app->basePath . $filepath)) {
            unlink(Yii::$app->basePath . $filepath);
        }
        else {
            throw new DomainException("Файл по пути $filepath не найден");
        }
    }
}