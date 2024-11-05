<?php

namespace common\models\scaffold;
use Yii;

/**
 * This is the model class for table "foreign_event".
 *
 * @property int $id
 * @property int $order_participant_id
 * @property string $name
 * @property int|null $organizer_id
 * @property string $begin_date
 * @property string $end_date
 * @property string|null $city
 * @property int|null $format
 * @property int|null $level
 * @property int|null $minister
 * @property int|null $min_age
 * @property int|null $max_age
 * @property string|null $key_words
 */
class ForeignEvent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foreign_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'begin_date', 'end_date'], 'required'],
            [['order_participant_id', 'organizer_id', 'format', 'level', 'minister', 'min_age', 'max_age'], 'integer'],
            [['begin_date', 'end_date'], 'safe'],
            [['name', 'city', 'key_words'], 'string', 'max' => 128],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_participant_id' => 'Order Participant ID',
            'name' => 'Name',
            'organizer_id' => 'Organizer ID',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'city' => 'City',
            'format' => 'Format',
            'level' => 'Level',
            'minister' => 'Minister',
            'min_age' => 'Min Age',
            'max_age' => 'Max Age',
            'key_words' => 'Key Words',
        ];
    }
}