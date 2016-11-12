<?php

use yii\db\Migration;

class m161005_200401_add_new_restaurant_table extends Migration
{
    public function up()
    {
        $this->createTable('new_restaurant', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(255)->notNull(),
            'phone' => $this->string(255),
            'note' => $this->string(500),
            'client_id' => $this->integer(10)->unsigned(),
            'deleted_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->null(),
        ], 'ENGINE = InnoDB');

        $this->createIndex('IDX_New_Restaurant_Client', 'new_restaurant', 'client_id');
        $this->addForeignKey('FK_New_Restaurant_Client', 'new_restaurant', 'client_id', 'clients', 'id');
    }

    public function down()
    {
        echo "m161005_200401_add_new_restaurant_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
