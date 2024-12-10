<?php

namespace common\repositories\educational;

use common\components\traits\CommonDatabaseFunctions;
use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\educational\training_group\DeleteTeachersFromGroupEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\educational\training_group\TrainingGroupLessonWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;

class TrainingGroupRepository
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

    public function get($id)
    {
        return TrainingGroupWork::find()->where(['id' => $id])->one();
    }

    public function getParticipants($id)
    {
        return TrainingGroupParticipantWork::find()->where(['training_group_id' => $id])->all();
    }

    public function getLessons($id)
    {
        return TrainingGroupLessonWork::find()->where(['training_group_id' => $id])->all();
    }

    public function save(TrainingGroupWork $group)
    {
        if (!$group->save()) {
            throw new DomainException('Ошибка сохранения учебной группы. Проблемы: '.json_encode($group->getErrors()));
        }
        return $group->id;
    }

    public function delete(TrainingGroupWork $model)
    {
        /** @var TrainingGroupWork $model */
        $model->recordEvent(new DeleteTeachersFromGroupEvent($model->id), get_class($model));

        $photos = $this->filesRepository->get(TrainingGroupWork::tableName(), $model->id, FilesHelper::TYPE_PHOTO);
        $presentations = $this->filesRepository->get(TrainingGroupWork::tableName(), $model->id, FilesHelper::TYPE_PRESENTATION);
        $works = $this->filesRepository->get(TrainingGroupWork::tableName(), $model->id, FilesHelper::TYPE_WORK);

        if (is_array($photos)) {
            foreach ($photos as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($presentations)) {
            foreach ($presentations as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        if (is_array($works)) {
            foreach ($works as $file) {
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->releaseEvents();

        return $model->delete();
    }
}