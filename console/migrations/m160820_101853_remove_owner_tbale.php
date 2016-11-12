<?php

use yii\db\Migration;

class m160820_101853_remove_owner_tbale extends Migration
{
    public function up()
    {
        $this->dropForeignKey(
            'restaurants_owner_id_foreign',
            'restaurants'
        );

        $this->dropIndex(
            'restaurants_owner_id_foreign',
            'restaurants'
        );

        $this->dropTable('owners');
        $this->dropColumn('restaurants','owner_id');
        $this->addColumn('restaurants','contact_number', $this->string(255)->notNull());
    }

    public function down()
    {
        echo "m160820_101853_remove_owner_tbale cannot be reverted.\n";

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
