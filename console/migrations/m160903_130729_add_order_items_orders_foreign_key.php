<?php

use yii\db\Migration;

class m160903_130729_add_order_items_orders_foreign_key extends Migration
{
    public function up()
    {
        $this->createIndex('IDX_Order_Items_Orders', 'order_items', 'order_id');
        $this->addForeignKey('FK_Order_Items_Orders', 'order_items', 'order_id', 'orders', 'id');
    }

    public function down()
    {
        echo "m160903_130729_add_order_items_orders_foreign_key cannot be reverted.\n";

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
