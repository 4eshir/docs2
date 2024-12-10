<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "training_group".
 *
 * @property int $id
 * @property string|null $number
 * @property int|null $training_program_id
 * @property int|null $teacher_id
 * @property string|null $start_date
 * @property string|null $finish_date
 * @property int|null $open
 * @property int|null $budget
 * @property int|null $branch
 * @property int|null $order_stop
 * @property int|null $archive
 * @property string|null $protection_date
 * @property int|null $protection_confirm
 * @property int|null $is_network
 * @property int|null $state 0 - заполнены основные данные, 1 - загружен контингент, 2 - загружено расписание, 3 - заполнены данные о защите, 4 - выданы сертификаты, группа отчислена и архивирована
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 *
 * @property User $creator
 * @property GroupProjectThemes[] $groupProjectThemes
 * @property User $lastEdit
 * @property PeopleStamp $teacher
 * @property TeacherGroup[] $teacherGroups
 * @property TrainingGroupExpert[] $trainingGroupExperts
 * @property TrainingGroupLesson[] $trainingGroupLessons
 * @property TrainingGroupParticipant[] $trainingGroupParticipants
 * @property TrainingProgram $trainingProgram
 */
class TrainingGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_program_id', 'teacher_id', 'open', 'budget', 'branch', 'order_stop', 'archive', 'protection_confirm', 'is_network', 'state', 'creator_id', 'last_edit_id'], 'integer'],
            [['start_date', 'finish_date', 'protection_date'], 'safe'],
            [['number'], 'string', 'max' => 64],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgram::class, 'targetAttribute' => ['training_program_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => PeopleStamp::class, 'targetAttribute' => ['teacher_id' => 'id']],
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
            'number' => 'Number',
            'training_program_id' => 'Training Program ID',
            'teacher_id' => 'Teacher ID',
            'start_date' => 'Start Date',
            'finish_date' => 'Finish Date',
            'open' => 'Open',
            'budget' => 'Budget',
            'branch' => 'Branch',
            'order_stop' => 'Order Stop',
            'archive' => 'Archive',
            'protection_date' => 'Protection Date',
            'protection_confirm' => 'Protection Confirm',
            'is_network' => 'Is Network',
            'state' => 'State',
            'creator_id' => 'Creator ID',
            'last_edit_id' => 'Last Edit ID',
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
     * Gets query for [[GroupProjectThemes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupProjectThemes()
    {
        return $this->hasMany(GroupProjectThemes::class, ['training_group_id' => 'id']);
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
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(PeopleStamp::class, ['id' => 'teacher_id']);
    }

    /**
     * Gets query for [[TeacherGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherGroups()
    {
        return $this->hasMany(TeacherGroup::class, ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroupExperts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupExperts()
    {
        return $this->hasMany(TrainingGroupExpert::class, ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroupLessons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLessons()
    {
        return $this->hasMany(TrainingGroupLesson::class, ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipants()
    {
        return $this->hasMany(TrainingGroupParticipant::class, ['training_group_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgram()
    {
        return $this->hasOne(TrainingProgram::class, ['id' => 'training_program_id']);
    }
}
