<?php

use yii\db\Migration;

class m160923_085734_udpate_addresses extends Migration
{
    public function up()
    {
        $this->dropColumn('addresses', 'address');
        $this->addColumn('addresses', 'building_name', $this->string(255)->notNull());
        $this->addColumn('addresses', 'floor_unit', $this->string(255)->notNull());
        $this->addColumn('addresses', 'street_no', $this->string(255)->notNull());
        $this->addColumn('addresses', 'postcode', $this->string(255));
        $this->addColumn('addresses', 'company', $this->string(255));
        $this->addColumn('addresses', 'label', $this->string(255)->notNull());
    }

    public function down()
    {
        echo "m160923_085734_udpate_addresses cannot be reverted.\n";

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
