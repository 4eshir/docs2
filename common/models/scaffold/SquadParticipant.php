<?php

namespace common\models\scaffold;
use Yii;
/**
 * This is the model class for table "squad_participant".
 *
 * @property int $id
 * @property int $act_participant_id
 * @property int $participant_id
 */
class SquadParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'squad_participant';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['act_participant_id', 'participant_id'], 'required'],
            [['act_participant_id', 'participant_id'], 'integer'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'act_participant_id' => 'Act Participant ID',
            'participant_id' => 'Participant ID',
        ];
    }
}
