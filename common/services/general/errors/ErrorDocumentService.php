<?php

namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\helpers\files\FilesHelper;
use common\models\work\ErrorsWork;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\order\OrderEventGenerateRepository;
use common\repositories\order\OrderMainRepository;
use frontend\models\work\order\DocumentOrderWork;
use Yii;

class ErrorDocumentService
{
    private ErrorsRepository $errorsRepository;
    private DocumentOrderRepository $orderRepository;
    private OrderTrainingGroupParticipantRepository $orderParticipantRepository;
    private OrderEventGenerateRepository $eventGenerateRepository;
    private ForeignEventRepository $foreignEventRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        DocumentOrderRepository $orderRepository,
        OrderTrainingGroupParticipantRepository $orderParticipantRepository,
        OrderEventGenerateRepository $eventGenerateRepository,
        ForeignEventRepository $foreignEventRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->orderRepository = $orderRepository;
        $this->orderParticipantRepository = $orderParticipantRepository;
        $this->eventGenerateRepository = $eventGenerateRepository;
        $this->foreignEventRepository = $foreignEventRepository;
    }

    // Проверка на отсутствие скана
    public function makeDocument_001($rowId)
    {
        $order = $this->orderRepository->get($rowId);
        if (count($order->getFileLinks(FilesHelper::TYPE_SCAN)) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_001,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_001)->getErrorState()
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

    public function makeDocument_002($rowId)
    {
        // deprecated
    }

    public function fixDocument_002($errorId)
    {
        // deprecated
    }

    // Проверка на отсутствие ключевых слов
    public function makeDocument_003($rowId)
    {
        $order = $this->orderRepository->get($rowId);
        if (is_null($order->key_words) || strlen($order->key_words) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_003,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_003)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_003($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $order = $this->orderRepository->get($error->table_row_id);
        if (!(is_null($order->key_words) || strlen($order->key_words) == 0)) {
            $this->errorsRepository->delete($error);
        }
    }

    public function makeDocument_004($rowId)
    {
        // deprecated
    }

    public function fixDocument_004($errorId)
    {
        // deprecated
    }

    // Проверка на наличие обучающихся, прикрепленных к приказу
    public function makeDocument_005($rowId)
    {
        $orderParticipant = $this->orderParticipantRepository->getByOrderIds($rowId);
        if (count($orderParticipant) == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_005,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_005)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_005($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $orderParticipant = $this->orderParticipantRepository->getByOrderIds($error->table_row_id);
        if (count($orderParticipant) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на связанное мероприятия (наличие информации для генерации мероприятия)
    public function makeDocument_006($rowId)
    {
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($rowId);
        if (!$foreignEvent) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_006,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_006)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_006($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($error->table_row_id);
        if ($foreignEvent) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на наличие данных для генерации документа приказа
    public function makeDocument_007($rowId)
    {
        $generateData = $this->eventGenerateRepository->getByOrderId($rowId);
        if (!$generateData) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_007,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_007)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_007($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $generateData = $this->eventGenerateRepository->getByOrderId($error->table_row_id);
        if ($generateData) {
            $this->errorsRepository->delete($error);
        }
    }
}