<?php

use yii\db\Migration;

class m160820_110315_add_create_udpate_delete_LastLogin_times extends Migration
{
    public function up()
    {
        $this->addColumn('addons','deleted_at', $this->timestamp()->null());
        $this->addColumn('addons','created_at', $this->timestamp()->null());
        $this->addColumn('addons','updated_at', $this->timestamp()->null());
        $this->addColumn('areas','created_at', $this->timestamp()->null());
        $this->addColumn('areas','updated_at', $this->timestamp()->null());
        $this->addColumn('blacklisted_clients','deleted_at', $this->timestamp()->null());
        $this->addColumn('clients','deleted_at', $this->timestamp()->null());
        $this->addColumn('commissions','deleted_at', $this->timestamp()->null());
        $this->addColumn('cuisines','created_at', $this->timestamp()->null());
        $this->addColumn('cuisines','updated_at', $this->timestamp()->null());
        $this->addColumn('favorite_restaurants','deleted_at', $this->timestamp()->null());
        $this->addColumn('item_choices','deleted_at', $this->timestamp()->null());
        $this->addColumn('item_choices','created_at', $this->timestamp()->null());
        $this->addColumn('item_choices','updated_at', $this->timestamp()->null());
        $this->addColumn('menu_categories','created_at', $this->timestamp()->null());
        $this->addColumn('menu_categories','updated_at', $this->timestamp()->null());
        $this->addColumn('orders','deleted_at', $this->timestamp()->null());
        $this->addColumn('order_items','deleted_at', $this->timestamp()->null());
        $this->addColumn('payment_methods','deleted_at', $this->timestamp()->null());
        $this->addColumn('payment_methods','created_at', $this->timestamp()->null());
        $this->addColumn('payment_methods','updated_at', $this->timestamp()->null());
        $this->addColumn('restaurants','deleted_at', $this->timestamp()->null());
        $this->addColumn('feedbacks','deleted_at', $this->timestamp()->null());
        $this->addColumn('reviews','deleted_at', $this->timestamp()->null());
        $this->addColumn('vouchers','deleted_at', $this->timestamp()->null());
        $this->addColumn('states','deleted_at', $this->timestamp()->null());
        $this->addColumn('states','created_at', $this->timestamp()->null());
        $this->addColumn('states','updated_at', $this->timestamp()->null());
        $this->addColumn('user','deleted_at', $this->timestamp()->null());
        $this->addColumn('user','last_logged_at', $this->timestamp()->null());
    }

    public function down()
    {
        echo "m160820_110315_add_create_udpate_delete_LastLogin_times cannot be reverted.\n";

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
