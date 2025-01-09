<?php

namespace common\models\scaffold;

use Yii;

/**
 * This is the model class for table "visit".
 *
 * @property int $id
 * @property int|null $training_group_id
 * @property int|null $participant_id
 * @property string|null $lessons
 *
 * @property ForeignEventParticipants $participant
 */
class Visit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['participant_id', 'training_group_id'], 'integer'],
            [['lessons'], 'string'],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::class, 'targetAttribute' => ['participant_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::class, 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participant_id' => 'Participant ID',
            'lessons' => 'Lessons',
        ];
    }

    /**
     * Gets query for [[Participant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(ForeignEventParticipants::class, ['id' => 'participant_id']);
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(TrainingGroup::class, ['id' => 'training_group_id']);
    }
}
