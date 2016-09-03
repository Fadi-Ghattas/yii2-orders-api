<?php

use yii\db\Migration;

class m160903_061806_add_order_status extends Migration
{
    public function up()
    {

        $this->addColumn('states', 'country_id', $this->integer(11)->unsigned()->notNull()->defaultValue(1));
        $this->alterColumn('states', 'country_id', $this->integer(11)->unsigned()->notNull());

        $this->createIndex('IDX_States_Countries', 'states', 'country_id');
        $this->addForeignKey('FK_States_Countries', 'states', 'country_id', 'countries', 'id');

        $this->insert('order_status', ['name' => 'Pending']);
        $this->insert('order_status', ['name' => 'Accepted']);
        $this->insert('order_status', ['name' => 'Declined']);
        $this->insert('order_status', ['name' => 'In-progress']);
        $this->insert('order_status', ['name' => 'Shipped']);
        $this->insert('order_status', ['name' => 'Delivered']);
        
    }

    public function down()
    {
        echo "m160903_061806_add_order_status cannot be reverted.\n";

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
