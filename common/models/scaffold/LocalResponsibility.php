<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "local_responsibility".
 *
 * @property int $id
 * @property int|null $responsibility_type
 * @property int|null $branch
 * @property int|null $auditorium_id
 * @property int|null $quant
 * @property int|null $people_id
 * @property int|null $regulation_id
 *
 * @property Auditorium $auditorium
 * @property People $people
 * @property Regulation $regulation
 */
class LocalResponsibility extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'local_responsibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['responsibility_type', 'branch', 'auditorium_id', 'quant', 'people_id', 'regulation_id'], 'integer'],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::class, 'targetAttribute' => ['auditorium_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['people_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::class, 'targetAttribute' => ['regulation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'responsibility_type' => 'Responsibility Type',
            'branch' => 'Branch',
            'auditorium_id' => 'Auditorium ID',
            'quant' => 'Quant',
            'people_id' => 'People ID',
            'regulation_id' => 'Regulation ID',
        ];
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::class, ['id' => 'auditorium_id']);
    }

    /**
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::class, ['id' => 'people_id']);
    }

    /**
     * Gets query for [[Regulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulation()
    {
        return $this->hasOne(Regulation::class, ['id' => 'regulation_id']);
    }
}
