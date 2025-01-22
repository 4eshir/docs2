<?php


namespace common\repositories\educational;


use frontend\models\work\CertificateTemplatesWork;

class CertificateTemplatesRepository
{
    public function get($id)
    {
        return CertificateTemplatesWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return CertificateTemplatesWork::find()->all();
    }
}