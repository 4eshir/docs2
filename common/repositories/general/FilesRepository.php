<?php

namespace common\repositories\general;

use common\models\work\general\FilesWork;

class FilesRepository
{
    public function get($tableName, $id, $fileType)
    {
        return FilesWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $id])
            ->andWhere(['file_type' => $fileType])
            ->all();
    }
}