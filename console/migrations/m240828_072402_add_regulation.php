<?php

use yii\db\Migration;

/**
 * Class m240828_072402_add_regulation
 */
class m240828_072402_add_regulation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('regulation', [
            'id' => $this->primaryKey(),
            'date' => $this->date(),
            'name' => $this->string(512),
            'short_name' => $this->string(256),
            'order_id' => $this->integer(),
            'ped_council_date' => $this->date(),
            'ped_council_number' => $this->integer(),
            'par_council_date' => $this->date(),
            'state' => $this->smallInteger()->comment('0 - утратило силу; 1 - актуально'),
            'regulation_type' => $this->smallInteger()->comment('1 - Положения, инструкции, правила; 2 - Положения о мероприятиях'),
            'scan' => $this->string(512),
            'creator_id' => $this->integer(),
            'last_edit_id' => $this->integer(),
            'created_at' => $this->timestamp(),
            'updated_at' => $this->timestamp()
        ]);

        $this->addForeignKey(
            'fk-regulation-1',
            'regulation',
            'creator_id',
            'user',
            'id',
            'RESTRICT',
        );

        $this->addForeignKey(
            'fk-regulation-2',
            'regulation',
            'last_edit_id',
            'user',
            'id',
            'RESTRICT',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-regulation-1',
            'regulation'
        );

        $this->dropForeignKey(
            'fk-regulation-2',
            'regulation'
        );

        $this->dropTable('regulation');

        return true;
    }
}
