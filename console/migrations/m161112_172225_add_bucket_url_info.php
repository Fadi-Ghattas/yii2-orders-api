<?php

use yii\db\Migration;

class m161112_172225_add_bucket_url_info extends Migration
{
    public function up()
    {
        $this->insert('setting', ['collection' => 'general', 'key' => 's3_bucket_url', 'value' => 'http://image.jommakan.asia/']);
    }

    public function down()
    {
        echo "m161112_172225_add_bucket_url_info cannot be reverted.\n";

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
