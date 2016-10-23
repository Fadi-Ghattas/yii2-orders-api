<?php

use yii\db\Migration;

class m161023_192420_add_anonymous_reviwes extends Migration
{
    public function up()
    {
        $this->alterColumn('reviews', 'client_id', $this->integer(10)->null()->unsigned());
    }

    public function down()
    {
        echo "m161023_192420_add_anonymous_reviwes cannot be reverted.\n";

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
