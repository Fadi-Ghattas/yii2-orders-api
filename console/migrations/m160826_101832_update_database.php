<?php

use yii\db\Migration;

class m160826_101832_update_database extends Migration
{
    public function up()
    {
        $this->alterColumn('clients', 'active', $this->boolean()->null()->defaultValue(1));
        $this->alterColumn('clients', 'status', $this->boolean()->null()->defaultValue(1));
        $this->alterColumn('clients', 'reg_id', $this->integer(11)->null());
        $this->addColumn('menu_items', 'is_verified', $this->boolean()->null()->defaultValue(0));
        $this->alterColumn('menu_items', 'is_taxable', $this->boolean()->null()->defaultValue(1));
        $this->addColumn('restaurants', 'is_verified_global', $this->boolean()->null()->defaultValue(0));
    }

    public function down()
    {
        echo "m160826_101832_update_database cannot be reverted.\n";

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
