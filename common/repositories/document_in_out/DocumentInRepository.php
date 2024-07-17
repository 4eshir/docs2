<?php

namespace common\repositories\document_in_out;

use common\models\work\document_in_out\DocumentInWork;

class DocumentInRepository
{
    public function get($id)
    {
        return DocumentInWork::find()->where(['id' => $id])->one();
    }
}