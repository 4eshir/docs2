<?php
namespace common\models\scaffold;
use Yii;
/**
 * This is the model class for table "act_participant".
 *
 * @property int $id
 * @property int $participant_id
 * @property int $teacher_id
 * @property int $teacher2_id
 * @property int $foreign_event_id
 * @property int $branch
 * @property int $focus
 * @property int $allow_remote_id
 * @property int $nomination
 */
class ActParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'act_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['participant_id', 'teacher_id', 'teacher2_id', 'foreign_event_id', 'branch', 'focus', 'allow_remote_id', 'nomination'], 'required'],
            [['participant_id', 'teacher_id', 'teacher2_id', 'foreign_event_id', 'branch', 'focus', 'allow_remote_id', 'nomination'], 'integer'],
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
            'teacher_id' => 'Teacher ID',
            'teacher2_id' => 'Teacher2 ID',
            'foreign_event_id' => 'Foreign Event ID',
            'branch' => 'Branch',
            'focus' => 'Focus',
            'allow_remote_id' => 'Allow Remote ID',
            'nomination' => 'Nomination',
        ];
    }
}