<?php

namespace common\repositories\regulation;

use common\helpers\files\FilesHelper;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\regulation\RegulationWork;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use DomainException;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\general\FileDeleteEvent;
use Yii;
use yii\db\ActiveRecord;

class RegulationRepository
{
    /**
     * @param $id
     * @return \yii\db\ActiveRecord|null
     */
    public function get($id)
    {
        return RegulationWork::find()->where(['id' => $id])->one();
    }

    public function getExpire($id)
    {

    }

    public function save(RegulationWork $regulation)
    {
        if (!$regulation->save()) {
            throw new DomainException('Ошибка сохранения положения. Проблемы: '.json_encode($regulation->getErrors()));
        }

        return $regulation->id;
    }
}