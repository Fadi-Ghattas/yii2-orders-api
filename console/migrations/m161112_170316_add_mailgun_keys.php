<?php

use yii\db\Migration;

class m161112_170316_add_mailgun_keys extends Migration
{
    public function up()
    {
        $this->insert('setting', ['collection' => 'general', 'key' => 'mail_gun_api_key', 'value' => 'key-758295c286588b30f777eb1d9d724f77']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'mail_gun_domain', 'value' => 'jommakan.asia']);
    }

    public function down()
    {
        echo "m161112_170316_add_mailgun_keys cannot be reverted.\n";

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
