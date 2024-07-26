<?php

namespace common\services\general\files;

use common\helpers\files\filenames\DocumentInFileNameGenerator;
use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use common\services\general\files\download\FileDownloadServer;
use common\services\general\files\download\FileDownloadYandexDisk;

class FileService
{
    private DocumentInFileNameGenerator $filenameGenerator;

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

    public function uploadFile($model, $file, $filetype, $basePath, $params = [])
    {
        // тут будет стратегия для загрузки на яндекс диск... потом

        //СРОЧНО - ЗДЕСЬ ДОЛЖЕН БЫТЬ ИВЕНТ ДЛЯ СОЗДАНИЯ ЗАПИСИ В ТАБЛИЦЕ FILES

        $file->saveAs($basePath . $this->filenameGenerator->generateFileName($model, $filetype, $params));
    }
}