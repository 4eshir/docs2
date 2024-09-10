<?php

namespace common\components\traits;

use Yii;

trait CommonRepositoryFunctions
{
    /**
     * Проверка возможности удаления записи из таблицы $dependentTable
     * @param string $baseTableName имя таблицы, которая связана с удаляемой
     * @param string $dependentTableName имя таблицы, из которой удаляется запись
     * @param int $entityId ID удаляемой записи
     * @return array
     */
    public function checkDeleteAvailable(string $baseTableName, string $dependentTableName, int $entityId)
    {
        $schema = Yii::$app->db->schema;
        $foreignKeys = $schema->getTableSchema($baseTableName)->foreignKeys;

        $errorStrings = [];

        foreach ($foreignKeys as $fkName => $fkInfo) {
            $keys = array_keys($fkInfo);
            $values = array_values($fkInfo);
            if ($values[0] === $dependentTableName) {
                $relatedDocumentsCount = Yii::$app->db->createCommand()
                    ->setSql("SELECT COUNT(*) FROM {$baseTableName} WHERE {$keys[1]} = :entityId")
                    ->bindValue(':entityId', $entityId)
                    ->queryScalar();

                if ($relatedDocumentsCount > 0) {
                    $name = Yii::$app->tables->get($baseTableName);
                    $key = $keys[1];
                    $errorStrings[] = "Нельзя удалить запись, так как существуют связанные записи в разделе \"{$name}\" ({$key})";
                }
            }
        }

        return $errorStrings;
    }
}