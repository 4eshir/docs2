<?php

use yii\db\Migration;

/**
 * Class m250129_143446_new_document_order_table
 */
class m250129_143446_new_document_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('document_order', [
            'id' => $this->primaryKey(),
            'order_copy_id' => $this->integer()->null(),
            'order_number' => $this->string(255)->null(),
            'order_postfix' => $this->integer()->null(),
            'order_name' => $this->string(255)->null(),
            'order_date' => $this->date()->null(),
            'signed_id' => $this->integer()->null(),
            'bring_id' => $this->integer()->null(),
            'executor_id' => $this->integer()->null(),
            'key_words' => $this->string(255)->null(),
            'creator_id' => $this->integer()->null(),
            'last_edit_id' => $this->integer()->null(),
            'type' => $this->integer()->null(),
            'state' => $this->integer()->null(),
            'nomenclature_id' => $this->integer()->null(),
            'study_type' => $this->integer()->null(),
        ]);

        // Добавление внешних ключей
        $this->addForeignKey('fk-document_order-signed_id', 'document_order', 'signed_id', 'people_stamp', 'id', 'RESTRICT' );
        $this->addForeignKey('fk-document_order-bring_id', 'document_order', 'bring_id', 'people_stamp', 'id', 'RESTRICT');
        $this->addForeignKey('fk-document_order-executor_id', 'document_order', 'executor_id', 'people_stamp', 'id', 'RESTRICT');
        $this->addForeignKey('fk-document_order-creator_id', 'document_order', 'creator_id', 'user', 'id', 'RESTRICT');
        $this->addForeignKey('fk-document_order-last_edit_id', 'document_order', 'last_edit_id', 'user', 'id', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-document_order-signed_id', 'document_order');
        $this->dropForeignKey('fk-document_order-bring_id', 'document_order');
        $this->dropForeignKey('fk-document_order-executor_id', 'document_order');
        $this->dropForeignKey('fk-document_order-creator_id', 'document_order');
        $this->dropForeignKey('fk-document_order-last_edit_id', 'document_order');
        $this->dropTable('document_order');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250129_143446_new_document_order_table cannot be reverted.\n";

        return false;
    }
    */
}
