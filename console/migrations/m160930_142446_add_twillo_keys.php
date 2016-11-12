<?php

use yii\db\Migration;

class m160930_142446_add_twillo_keys extends Migration
{
    public function up()
    {
        $this->insert('setting', ['collection' => 'general', 'key' => 'twillio_sid', 'value'=> 'AC6be9ed5be2fd7a4c51c4a91a1d0f89ba']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'twillio_token', 'value'=> '9d5b4295f9dc4e7521249aa1905928bf']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'twillio_number', 'value'=> '+19382232588']);
    }

    public function down()
    {
        echo "m160930_142446_add_twillo_keys cannot be reverted.\n";

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
