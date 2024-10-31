<?php

use yii\db\Migration;

/**
 * Class m241031_101903_add_act_participant_table
 */
class m241031_101903_add_act_participant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('act_participant', [
            'id' => $this->primaryKey(),
            'participant_id' => $this->integer()->notNull(),
            'teacher_id' => $this->integer()->notNull(),
            'teacher2_id' => $this->integer()->notNull(),
            'foreign_event_id' => $this->integer()->notNull(),
            'branch' => $this->integer()->notNull(),
            'focus' => $this->integer()->notNull(),
            'allow_remote_id' => $this->integer()->notNull(),
            'nomination' => $this->integer()->notNull(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropTable('act_participant');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241031_101903_add_act_participant_table cannot be reverted.\n";

        return false;
    }
    */
}
