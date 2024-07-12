<?php

namespace common\repositories\document_in_out;

use common\models\work\document_in_out\InOutDocumentsWork;

class InOutDocumentsRepository
{
    public function get($id)
    {
        return InOutDocumentsWork::find()->where(['id' => $id])->one();
    }
}