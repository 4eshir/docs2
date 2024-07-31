<?php

namespace frontend\events\general;

use DomainException;
use frontend\events\EventInterface;
use Yii;

class FileDeleteEvent implements EventInterface
{
    private $filepath;

    public function __construct(
        $filepath
    )
    {
        $this->filepath = $filepath;
    }

    public function isSingleton(): bool
    {
        return false;
    }

    public function execute()
    {
        if (file_exists(Yii::$app->basePath . $this->filepath)) {
            unlink(Yii::$app->basePath . $this->filepath);
        }
        else {
            throw new DomainException("Файл по пути $this->filepath не найден");
        }
    }
}