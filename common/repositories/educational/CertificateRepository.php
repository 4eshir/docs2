<?php


namespace common\repositories\educational;


use common\components\traits\CommonDatabaseFunctions;
use DomainException;
use frontend\models\work\CertificateTemplatesWork;
use frontend\models\work\educational\CertificateWork;

class CertificateRepository
{
    public function get($id)
    {
        return CertificateWork::find()->where(['id' => $id])->one();
    }

    public function getAll()
    {
        return CertificateWork::find()->all();
    }

    public function getCount()
    {
        return CertificateWork::find()->count();
    }

    public function save(CertificateWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения темы проекта. Проблемы: '.json_encode($model->getErrors()));
        }

        return $model->id;
    }
}