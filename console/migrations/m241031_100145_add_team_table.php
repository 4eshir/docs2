<?php

use yii\db\Migration;

/**
 * Class m241031_100145_add_team_table
 */
class m241031_100145_add_team_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('team', [
            'id' => $this->primaryKey(),
            'act_participant' => $this->integer()->notNull(),
            'foreign_event_id' => $this->integer()->notNull(),
            'participant_id' => $this->integer()->notNull(),
            'team_name_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('team');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241031_100145_add_team_table cannot be reverted.\n";

        return false;
    }
    */
}
