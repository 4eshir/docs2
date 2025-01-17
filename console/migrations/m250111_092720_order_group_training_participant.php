<?php

use yii\db\Migration;

/**
 * Class m250111_092720_order_group_training_participant
 */
class m250111_092720_order_group_training_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order_training_group_participant', [
            'id' => $this->primaryKey(),
            'training_group_participant_id' => $this->integer()->notNull(),
            'order_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('order_training_group_participant');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250111_092720_order_group_training_participant cannot be reverted.\n";

        return false;
    }
    */
}
