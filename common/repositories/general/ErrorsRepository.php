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
            ->andWhere(['was_amnesty' => 0])
            ->all();
    }

    public function getErrorsByTableRowsBranch(string $tableName, array $rowIds, int $branch = null)
    {
        $query = ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['IN', 'table_row_id', $rowIds])
            ->andWhere(['was_amnesty' => 0]);
        if ($branch) {
            $query = $query->andWhere(['branch' => $branch]);
        }

        return $query->all();
    }

    public function getErrorsByTableRowError(string $tableName, int $rowId, string $error)
    {
        return ErrorsWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $rowId])
            ->andWhere(['error' => $error])
            ->andWhere(['was_amnesty' => 0])
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
        if (!$this->getErrorsByTableRowError($model->table_name, $model->table_row_id, $model->error) || !is_null($model->id)) {
            if (!$model->save()) {
                throw new DomainException('Ошибка сохранения ошибки данных. Проблемы: '.json_encode($model->getErrors()));
            }
            return $model->id;
        }
        return false;
    }
}