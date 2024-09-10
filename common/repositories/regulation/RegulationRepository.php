<?php

namespace common\repositories\regulation;

use common\components\traits\CommonRepositoryFunctions;
use common\helpers\files\FilesHelper;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\general\FilesWork;
use frontend\models\work\regulation\RegulationWork;
use yii\db\ActiveRecord;

class RegulationRepository
{
    use CommonRepositoryFunctions;

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
        return RegulationWork::find()->where(['id' => $id])->one();
    }

    public function save(RegulationWork $regulation)
    {
        if (!$regulation->save()) {
            throw new DomainException('Ошибка сохранения положения. Проблемы: '.json_encode($regulation->getErrors()));
        }

        return $regulation->id;
    }

    public function delete(ActiveRecord $model)
    {
        /** @var RegulationWork $model */
        $scan = $this->filesRepository->get(RegulationWork::tableName(), $model->id, FilesHelper::TYPE_SCAN);

        if (is_array($scan)) {
            foreach ($scan as $file) {
                /** @var FilesWork $file */
                $this->fileService->deleteFile(FilesHelper::createAdditionalPath($file->table_name, $file->file_type) . $file->filepath);
                $model->recordEvent(new FileDeleteEvent($file->id), get_class($file));
            }
        }

        $model->releaseEvents();

        return $model->delete();
    }
}