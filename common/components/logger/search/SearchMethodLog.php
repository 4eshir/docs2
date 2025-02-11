<?php

namespace common\components\logger\search;

use common\repositories\log\LogRepository;
use yii\db\ActiveQuery;

class SearchMethodLog extends SearchLog implements SearchLogInterface
{
    /**
     * @var string[] $controllerNames
     * @var string[] $actionNames
     * @var int[] $callTypes
     */
    public array $controllerNames;
    public array $actionNames;
    public array $callTypes;

    /**
     * @param string[] $controllerNames
     * @return static
     */
    public static function byControllers(array $controllerNames)
    {
        $entity = new static();
        $entity->controllerNames = $controllerNames;
        return $entity;
    }

    /**
     * @param string[] $actionNames
     * @return static
     */
    public static function byActions(array $actionNames)
    {
        $entity = new static();
        $entity->actionNames = $actionNames;
        return $entity;
    }

    /**
     * @param int[] $callTypes
     * @return static
     */
    public static function byCallTypes(array $callTypes)
    {
        $entity = new static();
        $entity->callTypes = $callTypes;
        return $entity;
    }

    /**
     * @param int[] $levels
     * @param string $startDatetime
     * @param string $endDatetime
     * @param int[] $userIds
     * @param int[] $types
     * @param string $partText
     * @param string[] $controllerNames
     * @param string[] $actionNames
     * @param int[] $callTypes
     * @return static
     */
    public static function byParams(
        array $levels = [],
        string $startDatetime = '1900-01-01',
        string $endDatetime = '1900-01-01',
        array $userIds = [],
        array $types = [],
        string $partText = '',
        array $controllerNames = [],
        array $actionNames = [],
        array $callTypes = []
    )
    {
        $entity = new static();
        $entity->levels = $levels;
        $entity->startDatetime = $startDatetime;
        $entity->endDatetime = $endDatetime;
        $entity->userIds = $userIds;
        $entity->types = $types;
        $entity->partText = $partText;
        $entity->controllerNames = $controllerNames;
        $entity->actionNames = $actionNames;
        $entity->callTypes = $callTypes;

        return $entity;
    }

    public function createQuery(): ActiveQuery
    {
        $baseQuery = parent::createQuery();
        if (count($this->levels) > 0) {
            $baseQuery = $baseQuery->andWhere(['IN', 'level', $this->levels]);
        }
        if (count($this->userIds) > 0) {
            $baseQuery = $baseQuery->andWhere(['IN', 'user_id', $this->userIds]);
        }
        if (count($this->types) > 0) {
            $baseQuery = $baseQuery->andWhere(['IN', 'type', $this->types]);
        }
        if ($this->startDatetime != '1900-01-01') {
            $baseQuery = $baseQuery->andWhere(['>=', 'datetime', $this->startDatetime]);
        }
        if ($this->endDatetime != '1900-01-01') {
            $baseQuery = $baseQuery->andWhere(['<=', 'datetime', $this->endDatetime]);
        }
        if ($this->partText != '') {
            $baseQuery = $baseQuery->andWhere(['LIKE', 'text', $this->partText]);
        }

        return $baseQuery;
    }
}