<?php

namespace common\repositories\general;

use common\models\work\ErrorsWork;
use DomainException;

class ErrorsRepository
{
    public function get(int $id)
    {
        return ErrorsWork::find()->where(['id' => $id])->one();
    }

    public function getErrorsByTableRow(string $tableName, int $rowId)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $rowId])
            ->all();
    }

    public function getErrorsByTableRowError(string $tableName, int $rowId, string $error)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $rowId])
            ->andWhere(['error' => $error])
            ->one();
    }

    public function delete(ErrorsWork $model)
    {
        if (!$model->delete()) {
            var_dump($model->getErrors());
        }
        return $model->delete();
    }

    public function save(ErrorsWork $model)
    {
        if (!$this->getErrorsByTableRowError($model->table_name, $model->table_row_id, $model->error)) {
            if (!$model->save()) {
                throw new DomainException('Ошибка сохранения ошибки данных. Проблемы: '.json_encode($model->getErrors()));
            }
            return $model->id;
        }
        return false;
    }
}