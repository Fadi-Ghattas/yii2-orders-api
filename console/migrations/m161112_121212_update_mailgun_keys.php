<?php

use yii\db\Migration;

class m161112_121212_update_mailgun_keys extends Migration
{
    public function up()
    {   

        // delete email hello
        $this->delete('setting', ['key' => 'email']);
        $this->delete('setting', ['key' => 'email_name']);
        $this->delete('setting', ['key' => 'mail_gun_domain']);
        $this->delete('setting', ['key' => 'mail_gun_api_key']);
        
        $this->insert('setting', ['collection' => 'general', 'key' => 'hello_email', 'value' => 'ah.daleen@gmail.com']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'info_email', 'value' => 'ah.daleen@gmail.com']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'email_name', 'value' => 'FOODTIME']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'mail_gun_domain', 'value' => 'foodtime.asia']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'mail_gun_api_key', 'value' => 'key-758295c286588b30f777eb1d9d724f77']);
    }

    public function down()
    {
        echo "m161112_121212_update_mailgun_keys cannot be reverted.\n";

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
