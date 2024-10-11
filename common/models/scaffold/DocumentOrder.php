<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "document_order".
 *
 * @property int $id
 * @property int|null $order_copy_id
 * @property string|null $order_number
 * @property int|null $order_postfix
 * @property string|null $order_name
 * @property string|null $order_date
 * @property int|null $signed_id
 * @property int|null $bring_id
 * @property int|null $executor_id
 * @property string|null $key_words
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 * @property int|null $type
 * @property int|null $state
 * @property int|null $nomenclature_id
 * @property int|null $study_type
 *
 * @property People $bring
 * @property User $creator
 * @property People $executor
 * @property User $lastEdit
 * @property LegacyResponsible[] $legacyResponsibles
 * @property People $signed
 */
class DocumentOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_copy_id', 'order_postfix', 'signed_id', 'bring_id', 'executor_id', 'creator_id', 'last_edit_id', 'type', 'state', 'nomenclature_id', 'study_type'], 'integer'],
            [['order_date'], 'safe'],
            [['order_number', 'order_name', 'key_words'], 'string', 'max' => 255],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['signed_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['bring_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['last_edit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_copy_id' => 'Order Copy ID',
            'order_number' => 'Order Number',
            'order_postfix' => 'Order Postfix',
            'order_name' => 'Order Name',
            'order_date' => 'Order Date',
            'signed_id' => 'Signed ID',
            'bring_id' => 'Bring ID',
            'executor_id' => 'Executor ID',
            'key_words' => 'Key Words',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'type' => 'Type',
            'state' => 'State',
            'nomenclature_id' => 'Nomenclature ID',
            'study_type' => 'Study Type',
        ];
    }

    /**
     * Gets query for [[Bring]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBring()
    {
        return $this->hasOne(People::class, ['id' => 'bring_id']);
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(People::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[LastEdit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastEdit()
    {
        return $this->hasOne(User::class, ['id' => 'last_edit_id']);
    }

    /**
     * Gets query for [[LegacyResponsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLegacyResponsibles()
    {
        return $this->hasMany(LegacyResponsible::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(People::class, ['id' => 'signed_id']);
    }
}
