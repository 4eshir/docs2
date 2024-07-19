<?php

namespace common\services\general\files;

use Arhitector\Yandex\Disk;

class YandexDiskContext
{
    const OAUTH_TOKEN = 'y0_AgAEA7qkEK7HAAn5LwAAAADkMhh1CPjqd4DtS52DG7Vyd3i0JNf-NxY';

    static public function CheckSameFile($filepath)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);

        $resource = $disk->getResource('disk:/'.$filepath);

        return $resource->has();
    }

    static public function GetFileFromDisk($filepath)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);

        $resource = $disk->getResource($filepath);

        return $resource;

    }

    static public function UploadFileOnDisk($disk_filepath, $local_filepath)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);

        $resource = $disk->getResource($disk_filepath);

        $resource->upload($local_filepath);
    }

    static public function DeleteFileFromDisk($filepath)
    {
        $disk = new Disk(YandexDiskContext::OAUTH_TOKEN);

        $resource = $disk->getResource($filepath);

        return $resource->delete();
    }
}