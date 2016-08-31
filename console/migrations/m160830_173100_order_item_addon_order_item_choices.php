<?php

use yii\db\Migration;

class m160830_173100_order_item_addon_order_item_choices extends Migration
{
//    public function up()
//    {
//
//
//    }
//
//    public function down()
//    {
//        echo "m160830_173100_order_item_addon_order_item_choices cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->createTable('order_item_addon', [
            'order_item_id' => $this->integer(10)->unsigned()->notNull(),
            'addon_id' => $this->integer(10)->unsigned()->notNull(),
        ], 'ENGINE = InnoDB');

        $this->createIndex('IDX_Order_Item_Addon_Order_Item_id', 'order_item_addon', 'order_item_id');
        $this->addForeignKey('FK_Order_Item_Addon_Order_Item_id', 'order_item_addon', 'order_item_id', 'order_items', 'id');

        $this->createIndex('IDX_Order_Item_Addon_Addon_id', 'order_item_addon', 'addon_id');
        $this->addForeignKey('FK_Order_Item_Addon_Addon_id', 'order_item_addon', 'addon_id', 'addons', 'id');

        $this->addPrimaryKey('order_item_addon_key', 'order_item_addon', ['order_item_id', 'addon_id']);

        $this->createTable('order_item_choices', [
            'order_item_id' => $this->integer(10)->unsigned()->notNull(),
            'item_choice_id' => $this->integer(10)->unsigned()->notNull(),
        ], 'ENGINE = InnoDB');


        $this->createIndex('IDX_Order_Item_Choices_Order_Item_id', 'order_item_choices', 'order_item_id');
        $this->addForeignKey('FK_Order_Item_Choices_Order_Item_id', 'order_item_choices', 'order_item_id', 'order_items', 'id');

        $this->createIndex('IDX_Order_Item_Choices_Item_Choice_id', 'order_item_choices', 'item_choice_id');
        $this->addForeignKey('FK_Order_Item_Choices_Item_Choice_id', 'order_item_choices', 'item_choice_id', 'item_choices', 'id');

        $this->addPrimaryKey('order_item_choices_key', 'order_item_choices', ['order_item_id', 'item_choice_id']);


        $this->dropColumn('order_items', 'addon');
        //$this->truncateTable('item_choices');
        $this->dropForeignKey('order_items_choice_id_foreign', 'order_items');
        $this->dropColumn('order_items', 'choice_id');

        $this->alterColumn('orders', 'voucher_id', $this->integer(10)->unsigned()->null());
        $this->alterColumn('orders', 'total_with_voucher', $this->decimal(7, 2)->null()->defaultValue(0));


        $this->dropColumn('orders', 'status');
        $this->dropColumn('orders', 'status_id');
        $this->addColumn('orders', 'status_id', $this->integer(11)->unsigned()->notNull());


        $this->createTable('order_status', [
            'id' => $this->primaryKey()->unsigned()->notNull(),
            'name' => $this->string(255)->notNull(),
        ], 'ENGINE = InnoDB');

        $this->createIndex('IDX_Order_Status_Order_Item_id', 'orders', 'status_id');
        $this->addForeignKey('FK_Order_Status_Order_Item_id', 'orders', 'status_id', 'order_status', 'id');

    }

    public function safeDown()
    {
    }

}
