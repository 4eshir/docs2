<?php

namespace common\models\scaffold;

/**
 * @property int $id
 * @property int|null $order_copy_id
 * @property string|null $order_number
 * @property int|null $order_postfix
 * @property string $order_date
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
            [['local_number', 'local_date', 'real_date', 'document_theme', 'creator_id'], 'required'],
            [['local_number', 'local_postfix', 'correspondent_id', 'position_id', 'company_id', 'signed_id', 'get_id', 'send_method', 'creator_id', 'last_edit_id', 'need_answer'], 'integer'],
            [['local_date', 'real_date'], 'safe'],
            [['real_number'], 'string', 'max' => 64],
            [['document_theme', 'target'], 'string', 'max' => 256],
            [['key_words'], 'string', 'max' => 512],
            [['correspondent_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['correspondent_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['position_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['signed_id' => 'id']],
            [['get_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['get_id' => 'id']],
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
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * Gets query for [[Correspondent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondent()
    {
        return $this->hasOne(People::class, ['id' => 'correspondent_id']);
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
     * Gets query for [[Position]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'position_id']);
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
