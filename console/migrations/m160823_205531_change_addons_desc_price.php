<?php

use yii\db\Migration;

class m160823_205531_change_addons_desc_price extends Migration
{
    public function up()
    {
        $this->alterColumn('addons','description', $this->string(255)->null());
        $this->alterColumn('addons','price', $this->decimal(7,2)->null()->defaultValue(0));
    }

    public function down()
    {
        echo "m160823_205531_change_addons_desc_price cannot be reverted.\n";

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
