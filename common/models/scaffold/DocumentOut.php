<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "document_out".
 *
 * @property int $id
 * @property int $document_number
 * @property int|null $document_postfix
 * @property string $document_date
 * @property string $document_name
 * @property string $document_theme
 * @property int|null $correspondent_id
 * @property int|null $position_id
 * @property int|null $company_id
 * @property int|null $signed_id
 * @property int|null $executor_id
 * @property int|null $send_method
 * @property string $sent_date
 * @property int $creator_id
 * @property int|null $last_edit_id
 * @property string|null $key_words
 * @property int|null $is_answer
 *
 * @property Company $company
 * @property People $correspondent
 * @property User $creator
 * @property People $executor
 * @property User $lastEdit
 * @property Position $position
 * @property People $signed
 */
class DocumentOut extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_out';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_number', 'document_date', 'document_name', 'document_theme', 'sent_date', 'creator_id'], 'required'],
            [['document_number', 'document_postfix', 'correspondent_id', 'position_id', 'company_id', 'signed_id', 'executor_id', 'send_method', 'creator_id', 'last_edit_id', 'is_answer'], 'integer'],
            [['document_date', 'sent_date'], 'safe'],
            [['document_name'], 'string', 'max' => 64],
            [['document_theme'], 'string', 'max' => 256],
            [['key_words'], 'string', 'max' => 512],
            [['correspondent_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['correspondent_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['position_id' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['signed_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::class, 'targetAttribute' => ['executor_id' => 'id']],
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
            'document_number' => 'Document Number',
            'document_postfix' => 'Document Postfix',
            'document_date' => 'Document Date',
            'document_name' => 'Document Name',
            'document_theme' => 'Document Theme',
            'correspondent_id' => 'Correspondent ID',
            'position_id' => 'Position ID',
            'company_id' => 'Company ID',
            'signed_id' => 'Signed ID',
            'executor_id' => 'Executor ID',
            'send_method' => 'Send Method',
            'sent_date' => 'Sent Date',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
            'key_words' => 'Key Words',
            'is_answer' => 'Is Answer',
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
