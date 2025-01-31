<?php

use yii\db\Migration;

/**
 * Class m250131_063650_event_fixes
 */
class m250131_063650_event_fixes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('act_participant', 'branch');
        $this->addForeignKey(
            'fk-act_participant-3',
            'act_participant',
            'team_name_id',
            'team_name',
            'id',
            'RESTRICT'
        );
        $this->addForeignKey(
            'fk-act_participant-4',
            'act_participant',
            'foreign_event_id',
            'foreign_event',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-act_participant-4', 'act_participant');
        $this->dropForeignKey('fk-act_participant-3', 'act_participant');
        $this->addColumn('act_participant', 'branch', $this->integer());

        return true;
    }
}
