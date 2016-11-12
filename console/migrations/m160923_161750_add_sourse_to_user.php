<?php

use yii\db\Migration;

class m160923_161750_add_sourse_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user','source', $this->string(255));
    }

    public function down()
    {
        echo "m160923_161750_add_sourse_to_user cannot be reverted.\n";

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
