<?php

use yii\db\Migration;

class m160823_212031_add_image_background_to_restaurants extends Migration
{
    public function up()
    {
        $this->addColumn('restaurants', 'image_background', $this->string(255)->null());
    }

    public function down()
    {
        echo "m160823_212031_add_image_background_to_restaurants cannot be reverted.\n";

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
