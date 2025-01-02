<?php

namespace common\models\scaffold;

/**
 * @property int $id
 * @property int|null $order_copy_id
 * @property string|null $order_number
 * @property int|null $order_postfix
 * @property string $order_date
 * @property string $order_name
 * @property int|null $signed_id
 * @property int|null $bring_id
 * @property int|null $executor_id
 * @property string|null $key_words
 * @property int $creator_id
 * @property int|null $last_edit_id
 * @property string|null $target
 * @property int|null $type
 * @property int|null $state
 * @property int|null $nomenclature_id
 * @property int|null $study_type
 *
 *
 *
 * @property Company $company
 * @property People $correspondent
 * @property User $creator
 * @property User $get
 * @property User $lastEdit
 * @property Position $position
 * @property People $signed
 */

class OrderMain extends \yii\db\ActiveRecord
{
    public const ORDER_MAIN = 1;
    public const ORDER_EVENT = 2;
    public const ORDER_TRAINING = 3;
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
            [['order_date'], 'required'],
            [['order_copy_id', 'order_postfix', 'signed_id', 'bring_id', 'executor_id',  'creator_id', 'last_edit_id', 'nomenclature_id', 'type', 'state'], 'integer'],
            [['order_date'], 'safe'],
            [['order_number', 'order_name'], 'string', 'max' => 64],
            [['key_words'], 'string', 'max' => 512],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['signed_id' => 'id']],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['executor_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'order_date' => 'Order Date',
            'signed_id' => 'Signed ID',
            'bring_id' => 'Bring ID',
            'executor_id' => 'Executor ID',
            'key_words' => 'Key Words',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'target' => 'Target',
            'type' => 'Type',
            'state' => 'State',
            'nomenclature_id' => 'Nomenclature ID',
            'study_type' => 'Study Type',
        ];
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
     * Gets query for [[Get]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGet()
    {
        return $this->hasOne(User::class, ['id' => 'get_id']);
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
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(People::class, ['id' => 'signed_id']);
    }
    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }
    public function getBring()
    {
        return $this->hasOne(User::class, ['id' => 'bring_id']);
    }
}
