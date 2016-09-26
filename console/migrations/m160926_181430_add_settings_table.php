<?php

use yii\db\Migration;

class m160926_181430_add_settings_table extends Migration
{
    public function up()
    {
        $this->createTable('setting', [
            'id' => $this->primaryKey()->notNull(),
            'collection' => $this->string(128)->defaultValue('general'),
            'key' => $this->string(128),
            'value' => $this->string(128)
        ],'ENGINE = InnoDB');
        
        $this->insert('setting', ['collection' => 'general', 'key' => 'home_url', 'value'=> 'http://localhost/jommakan/']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'facebook_app_id', 'value'=>'1103564746358121']);
        $this->insert('setting', ['collection' => 'general', 'key' => 'facebook_app_secret', 'value'=>'f08b8be34c3fa16c55453e7ab91f3718']);
    }

    public function down()
    {
        echo "m160926_181430_add_settings_table cannot be reverted.\n";

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
