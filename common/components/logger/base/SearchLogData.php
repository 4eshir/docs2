<?php


class SearchLogData
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

    public static function byUserIds(array $userIds)
    {
        $entity = new static();
        $entity->userIds = $userIds;
        return $entity;
    }

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
}