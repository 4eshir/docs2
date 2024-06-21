<?php

use yii\db\Migration;

/**
 * Class m240621_081719_document_in_out
 */
class m240621_081719_document_in_out extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company', [
            'id' => $this->primaryKey(),
            'company_type' => $this->smallInteger()->comment('1 - образовательное учреждение, 2 - государственное учреждение, 3 - частная организация/ИП'),
            'name' => $this->string(128)->notNull(),
            'short_name' => $this->string(128)->notNull(),
            'is_contractor' => $this->bool()->notNull(),
            'inn' => $this->string(15),
            'category_smsp' => $this->smallInteger()->comment('1 - микропредприятие, 2 - малое предприятие, 3 - среднее предприятие, 4 - самозанятый, 5 - НЕ СМСП'),
            'comment' => $this->string(256),
            'last_edit_id' => $this->integer(),
            'phone_number' => $this->string(12),
            'email' => $this->string(256),
            'site' => $this->string(256),
            'ownership_type' => $this->smallInteger()->comment('1 - бюджетное, 2 - автономное, 3 - казённое, 4 - унитарное, 5 - НКО, 6 - нетиповое, 7 - ООО, 8 - ИП, 9 - ПАО, 10 - АО, 11 - ЗАО, 12 - физлицо, 13 - прочее'),
            'okved' => $this->string(12),
            'head_fio' => $this->string(256),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('company');

        return true;
    }
}
