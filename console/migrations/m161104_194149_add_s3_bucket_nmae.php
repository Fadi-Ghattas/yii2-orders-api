<?php

use yii\db\Migration;

class m161104_194149_add_s3_bucket_nmae extends Migration
{
    public function up()
    {
        $this->insert('setting', ['collection' => 'general', 'key' => 's3_bucket_name', 'value' => 'jommakan-all-images-s3']);
    }

    public function down()
    {
        echo "m161104_194149_add_s3_bucket_nmae cannot be reverted.\n";

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
