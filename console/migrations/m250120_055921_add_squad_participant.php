<?php

use yii\db\Migration;

/**
 * Class m250120_055921_add_squad_participant
 */
class m250120_055921_add_squad_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('squad_participant', [
            'id' => $this->primaryKey(),
            'act_participant_id' => $this->integer()->notNull(),
            'participant_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey(
            'fk-squad_participant-1',
            'squad_participant',
            'act_participant_id',
            'act_participant',
            'id',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk-squad_participant-2',
            'squad_participant',
            'participant_id',
            'foreign_event_participants',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-squad_participant-1', 'squad_participant');
        $this->dropForeignKey('fk-squad_participant-2', 'squad_participant');
        $this->dropTable('squad_participant');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250120_055921_add_squad_participant cannot be reverted.\n";

        return false;
    }
    */
}
