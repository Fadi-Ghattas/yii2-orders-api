<?php

use yii\db\Migration;

class m160912_084221_update_data_base extends Migration
{
    public function up()
    {
        $this->dropIndex('username','user');
        $this->createIndex('IDX_Phone_Number','clients','phone_number',true);
        $this->dropColumn('clients','status');
        $this->addColumn('clients','verified', $this->boolean()->null()->defaultValue(0));
    }

    public function down()
    {
        echo "m160912_084221_update_data_base cannot be reverted.\n";

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
