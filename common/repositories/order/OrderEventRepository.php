<?php
namespace common\repositories\order;
use app\models\work\order\OrderEventWork;
use DomainException;
use yii\web\UploadedFile;
class OrderEventRepository
{
    public function get($id)
    {
        return OrderEventWork::find()->where(['id' => $id])->one();
    }
    public function delete($id)
    {
        return OrderEventWork::deleteAll(['id' => $id]);
    }
    public function getAll()
    {
        return OrderEventWork::find()->all();
    }
    public function save(OrderEventWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }
}