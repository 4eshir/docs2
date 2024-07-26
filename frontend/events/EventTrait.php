<?php

namespace frontend\events;

use Exception;
use Yii;

trait EventTrait
{
    /**
     * Массив событий
     * @var array
     */
    protected $events = [];

    /**
     * Список запросов к базе данных для транзакции
     * @var array
     */
    protected $queries = [];

    public function recordEvent(EventInterface $event, $className)
    {
        if ($event->isSingleton() && $this->exist($className)) {
            return;
        }

        $this->events[] = $event;
    }

    protected function exist($className)
    {
        foreach ($this->events as $event) {
            if ($event instanceof $className) {
                return true;
            }
        }
        return false;
    }

    public function releaseEvents()
    {
        try {
            foreach ($this->events as $event) {
                /** @var EventInterface $event */
                $this->queries = array_merge($this->queries, $event->execute());
            }
        }
        catch (Exception $e) {
            Yii::error('Произошла ошибка в releaseEvents - ' . $e->getMessage());
        }

        $this->releaseQueries();
    }

    private function releaseQueries()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($this->queries as $query) {
                $command = Yii::$app->db->createCommand($query);
                $command->execute();
            }
            $transaction->commit();
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
        }
    }
}