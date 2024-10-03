<?php

namespace common\repositories\document_in_out;

use common\components\traits\CommonDatabaseFunctions;
use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\db\ActiveRecord;

class DocumentInRepository
{
    use CommonDatabaseFunctions;

    private FileService $fileService;
    private FilesRepository $filesRepository;

    public function __construct(
        FileService $fileService,
        FilesRepository $filesRepository
    )
    {
        $this->fileService = $fileService;
        $this->filesRepository = $filesRepository;
    }

    /**
     * @param $id
     * @return \yii\db\ActiveRecord|null
     */
    public function get($id)
    {
        return DocumentInWork::find()->where(['id' => $id])->one();
    }
    public function createReserve(DocumentInWork  $model)
    {
        $model->local_date = '2000-01-01';
        $model->real_date = '2000-01-01';
        $model->document_theme = 'Резерв';
    }
    public function setAnswer($model)
    {
        return $model->needAnswer;
    }
    public function getAllDocumentsDescDate()
    {
        return DocumentInWork::find()->orderBy(['local_date' => SORT_DESC])->all();
    }

    public function getAllDocumentsInYear()
    {
        return DocumentInWork::find()->where(['like', 'local_date', date('Y')])->orderBy(['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC])->all();
    }

    public function save(DocumentInWork $document)
    {
        if (!$document->save()) {
            throw new DomainException('Ошибка сохранения входящего документа. Проблемы: '.json_encode($document->getErrors()));
        }

        return $document->id;
    }

    public function delete(ActiveRecord $model)
    {
        /** @var DocumentInWork $model */
        $model->recordEvent(new InOutDocumentDeleteEvent($model->id), DocumentInWork::class);
        $scan = $this->filesRepository->get(DocumentInWork::tableName(), $model->id, FilesHelper::TYPE_SCAN);
        $docs = $this->filesRepository->get(DocumentInWork::tableName(), $model->id, FilesHelper::TYPE_DOC);
        $apps = $this->filesRepository->get(DocumentInWork::tableName(), $model->id, FilesHelper::TYPE_APP);

        if (is_array($scan)) {
            foreach ($scan as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($docs)) {
            foreach ($docs as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($apps)) {
            foreach ($apps as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->recordEvent(new InOutDocumentDeleteEvent($model->id), get_class($model));

        $model->releaseEvents();

        return $model->delete();
    }
}