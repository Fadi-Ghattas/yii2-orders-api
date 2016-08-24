<?php

use yii\db\Migration;

class m160823_213939_add_is_default_to_addresses extends Migration
{
    public function up()
    {
        $this->addColumn('addresses', 'is_default', $this->boolean()->null()->defaultValue(0));
    }

    public function down()
    {
        echo "m160823_213939_add_is_default_to_addresses cannot be reverted.\n";

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
