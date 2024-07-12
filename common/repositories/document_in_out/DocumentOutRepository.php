<?php

namespace common\repositories\document_in_out;

use common\models\work\document_in_out\DocumentOutWork;

class DocumentOutRepository
{
    public function get($id)
    {
        return DocumentOutWork::find()->where(['id' => $id])->one();
    }
}