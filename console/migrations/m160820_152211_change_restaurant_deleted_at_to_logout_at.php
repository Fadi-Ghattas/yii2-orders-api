<?php

use yii\db\Migration;

class m160820_152211_change_restaurant_deleted_at_to_logged_at extends Migration
{
    public function up()
    {
        $this->dropColumn('restaurants','deleted_at');
        $this->addColumn('restaurants','logout_at', $this->timestamp()->null());
    }

    public function down()
    {
        echo "m160820_152211_change_restaurant_deleted_at_to_logged_at cannot be reverted.\n";

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
