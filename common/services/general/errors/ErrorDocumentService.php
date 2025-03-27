<?php

namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\helpers\files\FilesHelper;
use common\models\work\ErrorsWork;
use common\repositories\general\ErrorsRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\order\OrderMainRepository;
use frontend\models\work\order\DocumentOrderWork;

class ErrorDocumentService
{
    private ErrorsRepository $errorsRepository;
    private DocumentOrderRepository $orderRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        DocumentOrderRepository $orderRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->orderRepository = $orderRepository;
    }

    public function makeDocument_001($rowId)
    {
        $order = $this->orderRepository->get($rowId);
        if (count($order->getFileLinks(FilesHelper::TYPE_SCAN)) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_001,
                    DocumentOrderWork::tableName(),
                    $rowId
                )
            );
        }
    }

    public function fixDocument_001($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $order = $this->orderRepository->get($error->table_row_id);
        if (count($order->getFileLinks(FilesHelper::TYPE_SCAN)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    public function makeDocument_002($errorId)
    {

    }

    public function fixDocument_002($errorId)
    {

    }

    public function makeDocument_003($errorId)
    {

    }

    public function fixDocument_003($errorId)
    {

    }

    public function makeDocument_004($errorId)
    {

    }

    public function fixDocument_004($errorId)
    {

    }

    public function makeDocument_005($errorId)
    {

    }

    public function fixDocument_005($errorId)
    {

    }

    public function makeDocument_006($errorId)
    {

    }

    public function fixDocument_006($errorId)
    {

    }

    public function makeDocument_007($errorId)
    {

    }

    public function fixDocument_007($errorId)
    {

    }
}