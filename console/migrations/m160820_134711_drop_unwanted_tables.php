<?php

use yii\db\Migration;

class m160820_134711_drop_unwanted_tables extends Migration
{
    public function up()
    {
        $this->dropTable('admin_role');
        $this->dropTable('admins');
        $this->dropTable('roles');
        $this->dropTable('migrations');
    }

    public function down()
    {
        echo "m160820_134711_drop_unwanted_tables cannot be reverted.\n";

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
