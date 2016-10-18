<?php

use yii\db\Migration;

class m161017_202614_add_s3_settings extends Migration
{
    public function up()
    {
        $this->insert('setting', ['collection' => 'general', 'key' => 's3_key', 'value'=> 'AKIAISPZVYQE7UQWKY2A']);
        $this->insert('setting', ['collection' => 'general', 'key' => 's3_secret', 'value'=>'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']);
    }

    public function down()
    {
        echo "m161017_202614_add_s3_settings cannot be reverted.\n";

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
