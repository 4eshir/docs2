<?php

namespace common\components\logger\search;

use common\repositories\log\LogRepository;
use Yii;
use yii\db\ActiveQuery;

class SearchLog implements SearchLogInterface
{
    /**
     * @var int[] $levels
     * @var int[] $userIds
     * @var int[] $types
     */
    public array $levels;
    public string $startDatetime;
    public string $endDatetime;
    public array $userIds;
    public array $types;
    public string $partText;

    /**
     * @param int[] $levels
     * @return static
     */
    public static function byLevels(array $levels)
    {
        $entity = new static();
        $entity->levels = $levels;
        return $entity;
    }

    public static function betweenDatetimes(string $startDatetime, string $endDatetime)
    {
        $entity = new static();
        $entity->startDatetime = $startDatetime;
        $entity->endDatetime = $endDatetime;
        return $entity;
    }

    /**
     * @param int[] $userIds
     * @return static
     */
    public static function byUserIds(array $userIds)
    {
        $entity = new static();
        $entity->userIds = $userIds;
        return $entity;
    }

    /**
     * @param int[] $types
     * @return static
     */
    public static function byTypes(array $types)
    {
        $entity = new static();
        $entity->types = $types;
        return $entity;
    }

    public static function byPartText(string $partText)
    {
        $entity = new static();
        $entity->partText = $partText;
        return $entity;
    }

    /**
     * @param int[] $levels
     * @param string $startDatetime
     * @param string $endDatetime
     * @param int[] $userIds
     * @param int[] $types
     * @param string $partText
     * @return static
     */
    public static function byParams(
        array $levels = [],
        string $startDatetime = '1900-01-01',
        string $endDatetime = '1900-01-01',
        array $userIds = [],
        array $types = [],
        string $partText = ''
    )
    {
        $entity = new static();
        $entity->levels = $levels;
        $entity->startDatetime = $startDatetime;
        $entity->endDatetime = $endDatetime;
        $entity->userIds = $userIds;
        $entity->types = $types;
        $entity->partText = $partText;
        return $entity;
    }

    public function createQuery(): ActiveQuery
    {
        $baseQuery = (Yii::createObject(LogRepository::class))->query();
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