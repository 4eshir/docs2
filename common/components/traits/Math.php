<?php

namespace common\components\traits;

use common\components\compare\AbstractCompare;
use InvalidArgumentException;
use Yii;

trait Math
{
    public function setDifference(array $set1, array $set2, string $compareClassname)
    {
        $compareModel = Yii::createObject($compareClassname);
        if (!($compareModel instanceof AbstractCompare)) {
            throw new InvalidArgumentException("$compareModel не является наследником AbstractCompare");
        }

        return array_udiff($set1, $set2, [$compareModel, 'compare']);
    }
}