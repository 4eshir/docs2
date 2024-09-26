<?php

namespace common\models\scaffold;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_people".
 * @property int $id
 * @property int $people_id
 * @property int|null $order_id
 */
class OrderPeople extends ActiveRecord
{
    public static function tableName()
    {
        return 'order_people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_id', 'order_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'people_id' => 'People ID',
        ];
    }
}