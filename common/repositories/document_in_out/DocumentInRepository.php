<?php

namespace common\repositories\document_in_out;

use common\models\work\document_in_out\DocumentInWork;

class DocumentInRepository
{
    public function get($id)
    {
        return DocumentInWork::find()->where(['id' => $id])->one();
    }

    public function getAllDocumentsDescDate()
    {
        return DocumentInWork::find()->orderBy(['local_date' => SORT_DESC])->all();
    }

    public function getAllDocumentsInYear()
    {
        return DocumentInWork::find()->where(['like', 'local_date', date('Y')])->orderBy(['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC])->all();
    }
}