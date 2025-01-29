<?php

namespace app\models\work\order;

use common\helpers\files\FilesHelper;
use common\models\scaffold\DocumentOrder;
use InvalidArgumentException;

class DocumentOrderWork extends DocumentOrder
{
    public const ORDER_MAIN = 1;
    public const ORDER_EVENT = 2;
    public const ORDER_TRAINING = 3;
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }
        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_SCAN:
                $addPath = FilesHelper::createAdditionalPath(OrderMainWork::tableName(), FilesHelper::TYPE_SCAN);
                break;
            case FilesHelper::TYPE_DOC:
                $addPath = FilesHelper::createAdditionalPath(OrderMainWork::tableName(), FilesHelper::TYPE_DOC);
                break;
            case FilesHelper::TYPE_APP:
                $addPath = FilesHelper::createAdditionalPath(OrderMainWork::tableName(), FilesHelper::TYPE_APP);
                break;
        }
        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }
    public function getFullOrderName(){
        return $this->order_number . ' ' . $this->order_postfix . ' ' . $this->order_name;
    }
}