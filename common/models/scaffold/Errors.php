<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "errors".
 *
 * @property int $id
 * @property string|null $number
 * @property string|null $description
 */
class Errors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'string', 'max' => 16],
            [['description'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'description' => 'Description',
        ];
    }
}
