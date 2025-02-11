<?php

namespace common\components\logger\search;

use yii\db\ActiveQuery;

interface SearchLogInterface
{
    /**
     * Метод, собирающий запрос к БД на основе свойств класса
     * @return ActiveQuery
     */
    public function createQuery() : ActiveQuery;
}