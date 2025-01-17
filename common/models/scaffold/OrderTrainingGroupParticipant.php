<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "order_training_group_participant".
 *
 * @property int $id
 * @property int $training_group_participant_id
 * @property int $order_id
 */
class OrderTrainingGroupParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_training_group_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'training_group_participant_id', 'order_id'], 'required'],
            [['id', 'training_group_participant_id', 'order_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_group_participant_id' => 'Training Group Participant ID',
            'order_id' => 'Order ID',
        ];
    }
}
