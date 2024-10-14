<?php

namespace frontend\models\work\dictionaries;

use common\events\EventTrait;
use common\helpers\files\FilesHelper;
use common\models\scaffold\Auditorium;
use InvalidArgumentException;

class AuditoriumWork extends Auditorium
{
    use EventTrait;

    public $filesList;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ]);
    }

    /**
     * Возвращает массив
     * link => форматированная ссылка на документ
     * id => ID записи в таблице files
     * @param $filetype
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getFileLinks($filetype)
    {
        if (!array_key_exists($filetype, FilesHelper::getFileTypes())) {
            throw new InvalidArgumentException('Неизвестный тип файла');
        }

        $addPath = '';
        switch ($filetype) {
            case FilesHelper::TYPE_OTHER:
                $addPath = FilesHelper::createAdditionalPath(AuditoriumWork::tableName(), FilesHelper::TYPE_OTHER);
                break;
        }

        return FilesHelper::createFileLinks($this, $filetype, $addPath);
    }
}