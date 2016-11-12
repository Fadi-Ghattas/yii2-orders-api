<?php

use yii\db\Migration;

class m160823_213400_remove_address_from_clients extends Migration
{
    public function up()
    {
        $this->dropForeignKey(
            'clients_address_id_foreign',
            'clients'
        );

        $this->dropIndex(
            'clients_address_id_foreign',
            'clients'
        );

        $this->dropColumn('clients', 'address_id');
        $this->alterColumn('clients', 'phone_number', $this->string(255)->null());
    }

    public function down()
    {
        echo "m160823_213400_remove_address_from_clients cannot be reverted.\n";

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
