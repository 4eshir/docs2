<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "russian_names".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $sex
 * @property int|null $peoples_count
 */
class RussianNames extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'russian_names';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['peoples_count'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['sex'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'sex' => 'Sex',
            'peoples_count' => 'Peoples Count',
        ];
    }
}
