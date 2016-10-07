<?php

use yii\db\Migration;

class m161007_103145_add_clienst_vouchers_tbale extends Migration
{
    public function up()
    {
        $this->createTable('clients_vouchers', [
            'client_id' => $this->integer(10)->unsigned()->notNull(),
            'voucher_id' => $this->integer(10)->unsigned()->notNull(),
        ], 'ENGINE = InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->addPrimaryKey('clients_vouchers_key', 'clients_vouchers', ['client_id', 'voucher_id']);

        $this->createIndex('IDX_ClientsVouchers_Clients', 'clients_vouchers', 'client_id');
        $this->addForeignKey('FK_ClientsVouchers_Clients', 'clients_vouchers', 'client_id', 'clients', 'id');

        $this->createIndex('IDX_ClientsVouchers_Vouchers', 'clients_vouchers', 'voucher_id');
        $this->addForeignKey('FK_ClientsVouchers_Vouchers', 'clients_vouchers', 'voucher_id', 'vouchers', 'id');

        $this->dropForeignKey(
            'FK_New_Restaurant_Client',
            'new_restaurant'
        );

        $this->dropIndex(
            'IDX_New_Restaurant_Client',
            'new_restaurant'
        );

        $this->dropTable('new_restaurant');

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
        ], 'ENGINE = InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $this->createIndex('IDX_New_Restaurant_Client', 'new_restaurant', 'client_id');
        $this->addForeignKey('FK_New_Restaurant_Client', 'new_restaurant', 'client_id', 'clients', 'id');

    }

    public function down()
    {
        echo "m161007_103145_add_clienst_vouchers_tbale cannot be reverted.\n";

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
