<?php

namespace common\services\general\files;

use common\helpers\FilesHelper;
use common\services\general\files\download\FileDownloadServer;
use common\services\general\files\download\FileDownloadYandexDisk;
use Yii;

class FileService
{
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

    public function uploadFile()
    {

    }
}