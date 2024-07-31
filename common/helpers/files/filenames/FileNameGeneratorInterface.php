<?php

namespace common\helpers\files\filenames;

interface FileNameGeneratorInterface
{
    public function generateFileName($object, $fileType, $params = []): string;
    public function getOrdinalFileNumber($object, $fileType);
}