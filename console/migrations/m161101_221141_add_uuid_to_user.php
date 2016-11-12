<?php

use yii\db\Migration;

class m161101_221141_add_uuid_to_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'uuid', $this->string(255)->null()->unique());
    }

    public function down()
    {
        echo "m161101_221141_add_uuid_to_user cannot be reverted.\n";

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
