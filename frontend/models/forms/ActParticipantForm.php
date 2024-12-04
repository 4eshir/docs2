<?php
namespace frontend\models\forms;
/**
 * This is the model class for table "act_participant".
 *
 * @property int $id
 * @property int|null $teacher_id
 * @property int|null $teacher2_id
 * @property int $branch
 * @property int $focus
 * @property int $type
 * @property string $nomination
 * @property int|null $team_name_id
 * @property int $foreign_event_id
 * @property int $allow_remote
 * @property int $form
 */
class ActParticipantForm extends \yii\base\Model
{
    public $participant;
    public $firstTeacher;
    public $secondTeacher;
    public $branch;
    public $focus;
    public $type;
    public $nomination;
    public $team;
    public $form;
    public $actFiles;
    /**
     * {@inheritdoc}
     */
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'teacher2_id', 'branch', 'focus', 'type', 'team_name_id', 'foreign_event_id', 'allow_remote', 'form'], 'integer'],
            [['branch', 'focus', 'type', 'nomination', 'foreign_event_id'], 'required'],
            [['nomination'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher ID',
            'teacher2_id' => 'Teacher2 ID',
            'branch' => 'Branch',
            'focus' => 'Focus',
            'type' => 'Type',
            'nomination' => 'Nomination',
            'team_name_id' => 'Team Name ID',
            'foreign_event_id' => 'Foreign Event ID',
            'allow_remote' => 'Allow Remote',
            'form' => 'Form',
        ];
    }
}