<?php

use yii\db\Migration;

/**
 * Class m240620_110736_first_wave
 */
class m240620_110736_first_wave extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('bot_message', [
            'id' => $this->primaryKey(),
            'text' => $this->text(),
        ]);

        $this->createTable('certificate_templates', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'path' => $this->string(256),
        ]);

        $this->createTable('characteristic_object', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'value_type' => $this->smallInteger()->comment('1 - целое, 2 - дробное, 3 - строковое, 4 - булево, 5 - дата, 6 - файл, 7 - выпадающий список'),
        ]);

        $this->createTable('complex', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
        ]);

        $this->createTable('entry', [
            'id' => $this->primaryKey(),
            'amount' => $this->integer(),
        ]);

        $this->createTable('errors', [
            'id' => $this->primaryKey(),
            'number' => $this->string(16),
            'description' => $this->string(128),
        ]);

        $this->createTable('event_external', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
        ]);

        $this->createTable('foreign_event_participants', [
            'id' => $this->primaryKey(),
            'firstname' => $this->string(64),
            'secondname' => $this->string(64),
            'patronymic' => $this->string(64),
            'birthdate' => $this->string(256),
            'sex' => $this->smallInteger()->comment('0 - мужской, 1 - женский'),
            'is_true' => $this->smallInteger(),
            'guaranteed_true' => $this->smallInteger(),
            'email' => $this->string(256),
        ]);

        $this->createTable('patchnotes', [
            'id' => $this->primaryKey(),
            'first_number' => $this->integer(),
            'second_number' => $this->integer(),
            'date' => $this->date(),
            'text' => $this->text(),
        ]);

        $this->createTable('position', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
        ]);

        $this->createTable('product_union', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'count' => $this->integer(),
            'average_price' => $this->double(),
            'average_cost' => $this->double(),
            'date' => $this->date(),
        ]);

        $this->createTable('project_theme', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'description' => $this->string(256),
        ]);

        $this->createTable('russian_names', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'sex' => $this->string(3),
            'peoples_count' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('bot_message');
        $this->dropTable('certificate_templates');
        $this->dropTable('characteristic_object');
        $this->dropTable('complex');
        $this->dropTable('entry');
        $this->dropTable('errors');
        $this->dropTable('event_external');
        $this->dropTable('foreign_event_participants');

        $this->dropTable('patchnotes');
        $this->dropTable('position');
        $this->dropTable('product_union');
        $this->dropTable('project_theme');
        $this->dropTable('russian_names');

        return true;
    }
}
