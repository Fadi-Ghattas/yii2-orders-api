<?php

use yii\db\Migration;

class m161010_200329_add_title_to_reviews extends Migration
{
    public function up()
    {
        $this->addColumn('reviews', 'title', $this->string(255)->notNull()->defaultValue('Great Food'));
        $this->alterColumn('reviews', 'title', $this->string(255)->notNull());
    }

    public function down()
    {
        echo "m161010_200329_add_title_to_reviews cannot be reverted.\n";

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
