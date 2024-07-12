<?php

namespace common\models\work\general;

use common\models\scaffold\People;
use InvalidArgumentException;
use yii\web\NotFoundHttpException;

class PeopleWork extends People
{
    const FIO_FULL = 1;
    const FIO_SURNAME_INITIALS = 2;
    const FIO_WITH_POSITION = 3;

    public static function getFioTypes()
    {
        return [
            self::FIO_FULL => 'ФИО полностью',
            self::FIO_SURNAME_INITIALS => 'Фамилия и инициалы',
            self::FIO_WITH_POSITION => 'ФИО полностью и должность с местом работы в скобках',
        ];
    }

    public function getFIO($type)
    {
        switch ($type) {
            case self::FIO_FULL:
                return $this->getFullFio();
            case self::FIO_SURNAME_INITIALS:
                return $this->getSurnameInitials();
            case self::FIO_WITH_POSITION:
                return $this->getFioPosition();
            default:
                throw new InvalidArgumentException('Неизвестный тип вывода ФИО');
        }
    }

    private function getFullFio()
    {
        return "$this->surname $this->firstname $this->patronymic";
    }

    private function getSurnameInitials()
    {
        return $this->surname . ' ' . substr($this->firstname, 0, 1) . ' ' . substr($this->patronymic, 0, 1);
    }

    private function getFioPosition()
    {
        return 'stub';
    }
}
