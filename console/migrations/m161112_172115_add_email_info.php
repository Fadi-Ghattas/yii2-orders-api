<?php

use yii\db\Migration;

class m161112_172115_add_email_info extends Migration
{
    public function up()
    {
        $this->insert('setting', ['collection' => 'general', 'key' => 'email', 'value' => 'hello@jommakan.asia']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'email_name', 'value' => 'FOOD HUNTING']);
    }

    public function down()
    {
        echo "m161112_172115_add_email_info cannot be reverted.\n";

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
