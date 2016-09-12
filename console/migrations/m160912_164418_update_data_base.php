<?php

use yii\db\Migration;

class m160912_164418_update_data_base extends Migration
{
    public function up()
    {
        $this->addColumn('restaurants','accepts_vouchers', $this->boolean()->null()->defaultValue(1));
    }

    public function down()
    {
        echo "m160912_164418_update_data_base cannot be reverted.\n";

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
