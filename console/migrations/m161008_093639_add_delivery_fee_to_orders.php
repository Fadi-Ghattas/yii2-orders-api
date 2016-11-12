<?php

use yii\db\Migration;

class m161008_093639_add_delivery_fee_to_orders extends Migration
{
    public function up()
    {
        $this->addColumn('orders', 'delivery_fee', $this->decimal(5,2)->notNull()->defaultValue(0));
    }

    public function down()
    {
        echo "m161008_093639_add_delivery_fee_to_orders cannot be reverted.\n";

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
