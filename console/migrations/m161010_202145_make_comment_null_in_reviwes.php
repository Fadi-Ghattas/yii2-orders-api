<?php

use yii\db\Migration;

class m161010_202145_make_comment_null_in_reviwes extends Migration
{
    public function up()
    {
        $this->alterColumn('reviews', 'comment', $this->string(255)->null());
    }

    public function down()
    {
        echo "m161010_202145_make_comment_null_in_reviwes cannot be reverted.\n";

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
